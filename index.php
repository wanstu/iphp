<?php

/**
 * 产品包装信息
 * 长: 68 cm
 *
 * 宽: 70 cm
 *
 * 高: 60 cm
 *
 * 重量: 23 kg
 *
 * 规则
 * 计算
 * 1 in（英寸）= 2.54 cm
 * 1 LB（磅）= 0.454 kg
 * 长度和重量转换时需要向上取整
 * 围长 = 最长边 + (次长边 + 第三边) * 2 （单位 in）
 * 体积重 = 最长边 * 次长边 * 第三边 / 体积重基数 （结果向上取整）
 * 体积重基数：250
 * 实重 = 产品重量（LB）和体积重之间取最大值
 * 类型定义
 * OUT_SPACE：（实重大于150）或（最长边大于108）或（围长大于165）
 * OVERSIZE：（围长大于130，小于等于165）或（最长边大于等于96小于108）
 * AHS：
 * WEIGHT: 实重大于50，小于等于150
 * SIZE: （围长大于105）或（最长边大于等于48，最长边小于108）或（次长边大于等于30）
 * 若两种类型都符合，则都输出
 * 当满足 OUT_SPACE 类型，不再判断 OVERSIZE 或 AHS；当满足 OVERSIZE，不再判断 AHS；
 * 关系：OUT_SPACE > OVERSIZE > AHS
 * 要求
 * 请实现类型的输出
 *
 * class Main()
 * {
 * public function test(float $length, float $width, float $height, float $weight): array
 * {
 *
 * }
 * }
 *
 * $obj = new Main();
 * var_dump($obj->test(68, 70, 60, 23));
 * 例如:
 *
 * 输入[68, 70, 60, 23], 输出[AHS-WEIGHT, AHS-SIZE]
 * 输入[114.50, 42, 26, 47.5], 输出[AHS-WEIGHT]
 * 输入[162, 60, 11, 14], 输出[AHS-SIZE]
 * 输入[113, 64, 42.5, 35.85], 输出[OVERSIZE]
 * 输入[114.5, 17, 51.5, 16.5], 输出[]
 */

class unitConvertHelper {

    private const IN_MM_RATIO = 25.4;
    private const LB_G_RATIO = 454;

    private const CONVERT_PRECISION = 2;

    /**
     * 毫米转英寸
     * @param $mm
     * @return float
     */
    public function mm2in($mm): float {
        if(!$this->_checkValidity($mm)) {
            // TODO 异常处理
            new Exception('mm is not valid', 1);
        }
        return (float)bcmul($mm, self::IN_MM_RATIO, self::CONVERT_PRECISION);
    }

    /**
     * 英寸转毫米
     * @param $in
     * @return float
     */
    public function in2mm($in): float {
        if(!$this->_checkValidity($in)) {
            // TODO 异常处理
            new Exception('inch is not valid', 2);
        }
        return (float)bcdiv($in, self::IN_MM_RATIO, self::CONVERT_PRECISION);
    }

    /**
     * 克转英镑
     * @param $g
     * @return float
     */
    public function g2lb($g): float {
        if(!$this->_checkValidity($g)) {
            // TODO 异常处理
            new Exception('g is not valid', 3);
        }
        return (float)bcdiv($g, self::LB_G_RATIO, self::CONVERT_PRECISION);
    }

    /**
     * 克转英镑
     * @param $lb
     * @return float
     */
    public function lb2g($lb): float {
        if(!$this->_checkValidity($lb)) {
            // TODO 异常处理
            new Exception('lb is not valid', 4);
        }
        return (float)bcdiv($lb, self::LB_G_RATIO, self::CONVERT_PRECISION);
    }



    private function _checkValidity($value): bool {
        if($value <= 0) {
            return false;
        }
        return true;
    }
}

class goods {

    private const BULK_WEIGHT_BASE = 250;


    /**
     * @var float 长度 单位 mm
     */
    private $length;


    /**
     * @var float 宽度 单位 mm
     */
    private $width;

    /**
     * @var float 高度 单位 mm
     */
    private $height;

    /**
     * @var float 围长 单位 mm
     */
    private $girth;

    /**
     * @var float 重量 单位 g
     */
    private $weight;

    /**
     * @var float 体积重 单位 g
     */
    private $bulkWeight;

    private unitConvertHelper $unitConvertHelper;

    public function __construct(int $length, int $width, int $height, int $weight) {
        $this->unitConvertHelper = new unitConvertHelper();

        $this->setLength($length)->setWidth($width)->setHeight($height)->setWeight($weight);

        $this->_computeGirth();

    }

    /**
     * 设置 长度
     * @param int $length 单位 mm
     * @return goods
     */
    public function setLength(int $length): goods {
        if(!$this->_checkValidity($length)) {
            // TODO 异常处理
            new Exception('length is not valid', 1);
        }
        $this->length = $length;
        return $this;
    }

    /**
     * 设置 宽度
     * @param int $width 单位 mm
     * @return goods
     */
    public function setWidth(int $width): goods {
        if(!$this->_checkValidity($width)) {
            // TODO 异常处理
            new Exception('width is not valid', 2);
        }
        $this->width = $width;
        return $this;
    }

    /**
     * 设置 高度
     * @param int $height 单位 mm
     * @return goods
     */
    public function setHeight(int $height): goods {
        if(!$this->_checkValidity($height)) {
            // TODO 异常处理
            new Exception('height is not valid', 3);
        }
        $this->height = $height;
        return $this;
    }

    /**
     * 设置 重量
     * @param int $weight 单位 g
     * @return goods
     */
    public function setWeight(int $weight): goods {
        if(!$this->_checkValidity($weight)) {
            // TODO 异常处理
            new Exception('height is not valid', 4);
        }
        $this->weight = $weight;
        return $this;
    }

    /**
     * 获取商品长度 mm
     * @return float
     */
    public function getLengthMm() {
        return $this->length;
    }

    /**
     * 获取商品宽度 mm
     * @return float
     */
    public function getWidthMm() {
        return $this->width;
    }

    /**
     * 获取商品高度 mm
     * @return float
     */
    public function getHeightMm() {
        return $this->height;
    }


    /**
     * 获取商品围长 mm
     * @return float
     */
    public function getGirthMm() {
        return $this->girth;
    }

    /**
     * 获取商品重量 g
     * @return float
     */
    public function getWeightG() {
        return $this->weight;
    }

    /**
     * 获取最长边长度 mm
     * @return mixed
     */
    private function _getLongestSide() {

        $arr = [$this->length, $this->width, $this->height];
        sort($arr, SORT_NUMERIC);

        return $arr[0];
    }

    /**
     * 获取次长边长度 mm
     * @return mixed
     */
    private function _getSecondLongestSide() {

        $arr = [$this->length, $this->width, $this->height];
        sort($arr, SORT_NUMERIC);

        return $arr[1];
    }

    /**
     * 获取第三长边长度 mm
     * @return mixed
     */
    private function _getThirdSide() {

        $arr = [$this->length, $this->width, $this->height];
        sort($arr, SORT_NUMERIC);

        return $arr[2];
    }

    /**
     * 计算围长
     */
    private function _computeGirth() {
        $this->girth = $this->_getLongestSide() + ($this->_getSecondLongestSide() + $this->_getThirdSide()) * 2;
    }


    /**
     * 检查输入数量的有效性
     * @param $value
     * @return bool
     */
    private function _checkValidity($value): bool {
        if($value <= 0) {
            return false;
        }
        return true;
    }


}
class Main {
    public function test(float $length, float $width, float $height, float $weight): array {

    }
}

$obj = new Main();
var_dump($obj->test(68, 70, 60, 23));