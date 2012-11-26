<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-07 19:54
 * Filename: IpConvert.class.php
 * Description:�Ӵ������ݿ⽫ip��ַת��Ϊ��ʵ�ĵ�ַ 
 */

class IpConvert {

    /**
     * ת����������ʵ��ַ
     */
    private $address = array();

    /**
     * �������ݿ����ڵĵ�ַ
     * @string
     */
    private $qqWryFile;

    public function __Construct($dataFile = 'qqwry.dat') {

        $this->qqWryFile = __DIR__ . DIRECTORY_SEPARATOR . $dataFile;
    }

    /**
     * �����е�ip����ת��Ϊ��ʵ�ĵ�ַ
     * @return mixed
     */
    public function convertIp($ip) {

        if(!$ip)
            return false;

        if(is_array($ip)) {
            foreach ($ip as $key => $value) {
                $this->convertIp($value);
            }
        } else {
            $this->address[$ip] = $this->_convert($ip);
        }

        return true;
    }

    /**
     * ��ȡת����Ľ��
     * @return array
     */
    public function getAddress() {

        return $this->address;
    }

    /**
     * ת��ipΪ����Ӧ������λ��
     * @param $ip string
     * @return $location string
     */
    private function _convert($ip) {

        $location = '';

        //var_dump($ip);
       // if(!preg_match('/^d{1,3}.d{1,3}.d{1,3}.d{1,3}$/', $ip)){
       //     throw new Exception('the ip address is not right!');
       //     return false;
       // }

        // ���ļ�qqWry.dat
        $fd = fopen($this->qqWryFile, 'rb');
        if(!$fd) {
            throw new Exception('the data can not open!');
            return false;
        }
        
        // �ֽ�IP�������㣬�ó�������
        $ip = explode('.', $ip);
        $ipInt = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

        // ��һ������ƫ�� ->  ֻ��ָ���ַ
        $indexBegin = fread($fd, 4);
        // ���һ�������ľ���ƫ�� -> ָ���ַ
        $indexEnd = fread($fd, 4);

        $ipBegin = implode('', unpack('L', $indexBegin));
        $ipEnd = implode('', unpack('L', $indexEnd));
        if($ipBegin < 0)
            $ipbegin += pow(2, 32);
        if($ipEnd < 0)
            $ipEnd += pow(2, 32);

        // ��ȡ������¼������
        $indexNum = ($ipEnd - $ipBegin)/7 + 1;

        //���ж��ֲ��� �趨���ҵĿ�ʼ�ͽ���λ��
        //ǰ���ip Ϊ$forwardIP, �����ipΪ$backwardIP
        //��ǰ��ip����Ҫ���ҵ�ip, ���ߺ���ipС��Ҫ���ҵ�ipʱ�����ҽ���
        $beginNum = 0;
        $endNum = $indexNum;

        do {
            $middle = intval(($beginNum + $endNum)/2);
            fseek($fd, $ipBegin + $middle * 7);
            $forward = fread($fd, 4);
            $forwardIP = implode('', unpack('L', $forward));
            if($forwardIP < 0)
                $forwardIP += pow(2, 32);
            // ���middle��IP ���� Ҫ���ҵ�ip ��˵��Ҫ���ҵ�ip��ǰ���
            if($forwardIP > $ipInt) {
                $endNum = $middle;
                continue;
            }

            // ���middle��IP С�� Ҫ���ҵ�ip ��˵��Ҫ���ҵ�ipҪô���ڵ�ǰmiddle���ڵ�ip�Σ�Ҫô������ip�ĺ���
            // �����ڵ�ǰmiddle��ip�β���
            $record = fread($fd, 3);
            // ??Ϊʲô��string�������ascii 0 ?? ��Ӧ����ǰ����
            $recordPointer = implode('', unpack('L', $record . chr(0)));
            fseek($fd, $recordPointer);
            $lastIP = fread($fd, 4);
            $backwardIP = implode('', unpack('L', $lastIP));
            if($backwardIP < 0)
                $backwardIP += pow(2, 32);

            if($backwardIP > $ipInt) {
                // ���ҳɹ����˳�ѭ��
                break;
            } else {
                if($beginNum == $middle) {
                    fclose($fd);
                    return 'Unknown Address';
                }
                $beginNum = $middle;
            }

        }while($forwardIP > $ipInt || $backwardIP < $ipInt);

        // ���ҵ��ɹ���IP��ַ֮�󣬽��и�IP��ַ��Ӧ�Ĺ��ҵ�����ת��
        $modeAddress = fread($fd, 1);        
        // �ض���ģʽ2
        if($modeAddress == chr(2)) {
            $addressPointer = fread($fd, 3);
            $addressPointer = implode('', unpack('L', $addressPointer . chr(0)));
            // ���Ȼ�õ���������
            while(($char = fread($fd, 1)) != chr(0)) {
                $district .= $char;
            }
            fseek($fd, $addressPointer);
            // ��ȡ���ҵ�����
            while(($char = fread($fd, 1)) != chr(0)) {
                $country .= $char;
            }

            $location = $country . ' ' . $district;
            return $location;
        }

        // �ض���ģʽ1
        if($modeAddress == chr(1)) {
            $addressPointer = fread($fd, 3);
            $addressPointer = implode('', unpack('L', $addressPointer . chr(0)));
            fseek($fd, $addressPointer);
            $mode = fread($fd, 1);
            if($mode != chr(2)) {
                $country = $mode;
                while(($char = fread($fd, 1)) != chr(0)) {
                    $country .= $char;
                }
                while(($char = fread($fd, 1)) != chr(0)) {
                    $district .= $char;
                }

                $location = $country . ' ' . $district;
                return $location;
            }
            // ���ڶ���ε�modeΪ2��ʱ��
            // ���ҵ�ƫ�Ƶ�ַ
            $countryPointer = fread($fd, 3);
            $countryPointer = implode('', unpack('L', $countryPointer . chr(0)));

            // ������ε�mode
            $modeLast = fread($fd, 1);
            if(($modeLast != chr(1)) && ($modeLast != chr(2))) {
                // ��ȡ��������
                $district = $modeLast;
                while(($char = fread($fd, 1)) != chr(0)) {
                    $district .= $char;
                }
                fseek($fd, $countryPointer);
                while(($char = fread($fd, 1)) != chr(0)) {
                    $country .= $char;
                }
                $location = $country . ' ' . $district;

                return $location;
            } else {
                $districtPointer = fread($fd, 3);
                $districtPointer = implode('', unpack('L', $districtPointer . chr(0)));
                fseek($fd, $districtPointer);
                while(($char = fread($fd, 1)) != chr(0)) {
                    $district .= $char;
                }
                fseek($fd, $countryPointer);
                while(($char = fread($fd, 1)) != chr(0)) {
                    $country .= $char;
                }
                $location = $country . ' ' . $district;

                return $location;
            }
        }

    }
}
