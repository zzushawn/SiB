<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-07 19:54
 * Filename: IpConvert.class.php
 * Description:从纯真数据库将ip地址转换为现实的地址 
 */

class IpConvert {

    /**
     * 转换出来的真实地址
     */
    private $address = array();

    /**
     * 纯真数据库所在的地址
     * @string
     */
    private $qqWryFile;

    public function __Construct($dataFile = 'qqwry.dat') {

        $this->qqWryFile = __DIR__ . DIRECTORY_SEPARATOR . $dataFile;
    }

    /**
     * 将类中的ip参数转换为真实的地址
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
     * 获取转换后的结果
     * @return array
     */
    public function getAddress() {

        return $this->address;
    }

    /**
     * 转换ip为所对应的物理位置
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

        // 打开文件qqWry.dat
        $fd = fopen($this->qqWryFile, 'rb');
        if(!$fd) {
            throw new Exception('the data can not open!');
            return false;
        }
        
        // 分解IP进行运算，得出整型数
        $ip = explode('.', $ip);
        $ipInt = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

        // 第一条索引偏移 ->  只是指针地址
        $indexBegin = fread($fd, 4);
        // 最后一条索引的绝对偏移 -> 指针地址
        $indexEnd = fread($fd, 4);

        $ipBegin = implode('', unpack('L', $indexBegin));
        $ipEnd = implode('', unpack('L', $indexEnd));
        if($ipBegin < 0)
            $ipbegin += pow(2, 32);
        if($ipEnd < 0)
            $ipEnd += pow(2, 32);

        // 获取索引记录的总数
        $indexNum = ($ipEnd - $ipBegin)/7 + 1;

        //进行二分查找 设定查找的开始和结束位置
        //前向的ip 为$forwardIP, 后向的ip为$backwardIP
        //当前向ip大于要查找的ip, 或者后向ip小于要查找的ip时，查找结束
        $beginNum = 0;
        $endNum = $indexNum;

        do {
            $middle = intval(($beginNum + $endNum)/2);
            fseek($fd, $ipBegin + $middle * 7);
            $forward = fread($fd, 4);
            $forwardIP = implode('', unpack('L', $forward));
            if($forwardIP < 0)
                $forwardIP += pow(2, 32);
            // 如果middle的IP 大于 要查找的ip 则说明要查找的ip在前半段
            if($forwardIP > $ipInt) {
                $endNum = $middle;
                continue;
            }

            // 如果middle的IP 小于 要查找的ip 则说明要查找的ip要么是在当前middle所在的ip段，要么在索引ip的后半段
            // 首先在当前middle的ip段查找
            $record = fread($fd, 3);
            // ??为什么在string后面加入ascii 0 ?? 不应该在前面吗？
            $recordPointer = implode('', unpack('L', $record . chr(0)));
            fseek($fd, $recordPointer);
            $lastIP = fread($fd, 4);
            $backwardIP = implode('', unpack('L', $lastIP));
            if($backwardIP < 0)
                $backwardIP += pow(2, 32);

            if($backwardIP > $ipInt) {
                // 查找成功，退出循环
                break;
            } else {
                if($beginNum == $middle) {
                    fclose($fd);
                    return 'Unknown Address';
                }
                $beginNum = $middle;
            }

        }while($forwardIP > $ipInt || $backwardIP < $ipInt);

        // 查找到成功的IP地址之后，进行该IP地址对应的国家地区的转换
        $modeAddress = fread($fd, 1);        
        // 重定向模式2
        if($modeAddress == chr(2)) {
            $addressPointer = fread($fd, 3);
            $addressPointer = implode('', unpack('L', $addressPointer . chr(0)));
            // 首先获得地区的名称
            while(($char = fread($fd, 1)) != chr(0)) {
                $district .= $char;
            }
            fseek($fd, $addressPointer);
            // 获取国家的名称
            while(($char = fread($fd, 1)) != chr(0)) {
                $country .= $char;
            }

            $location = $country . ' ' . $district;
            return $location;
        }

        // 重定向模式1
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
            // 当第二层次的mode为2的时候
            // 国家的偏移地址
            $countryPointer = fread($fd, 3);
            $countryPointer = implode('', unpack('L', $countryPointer . chr(0)));

            // 第三层次的mode
            $modeLast = fread($fd, 1);
            if(($modeLast != chr(1)) && ($modeLast != chr(2))) {
                // 获取地区名称
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
