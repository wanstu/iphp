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

class UnitConvertHelper {
    private const IN_CM_RATIO = 2.54;
    private const LB_KG_RATIO = 0.454;

    private const CONVERT_PRECISION = 2;

    /**
     * 厘米转英寸
     * @param $cm
     * @return float
     */
    public function cm2in($cm): float {
        if(!$this->_checkValidity($cm)) {
            // TODO 异常处理
            new Exception('cm is not valid', 5);
        }
        return (float)bcdiv($cm, self::IN_CM_RATIO, self::CONVERT_PRECISION);
    }

    /**
     * 千克转镑
     * @param $kg
     * @return float
     */
    public function kg2lb($kg): float {
        if(!$this->_checkValidity($kg)) {
            // TODO 异常处理
            new Exception('kg is not valid', 7);
        }
        return (float)bcdiv($kg, self::LB_KG_RATIO, self::CONVERT_PRECISION);
    }


    private function _checkValidity($value): bool {
        if($value <= 0) {
            return false;
        }
        return true;
    }
}


class Goods {

    /**
     * 体积重基数
     */
    private const BULK_WEIGHT_BASE = 250;


    /**
     * @var int 长度 单位 mm
     */
    private int $length;


    /**
     * @var int 宽度 单位 mm
     */
    private int $width;

    /**
     * @var int 高度 单位 mm
     */
    private int $height;

    /**
     * @var int 围长 单位 mm
     */
    private int $girth;

    /**
     * @var int 重量 单位 g
     */
    private int $weight;

    /**
     * @var int 体积重 单位 g
     */
    private int $bulkWeight;


    /**
     * 货物的类型标签
     * @var array
     */
    private array $tag = [];

    /**
     * 单位转换助手
     * @var UnitConvertHelper
     */
    private UnitConvertHelper $unitConvertHelper;

    public function __construct(int $length, int $width, int $height, int $weight) {

        $this->unitConvertHelper = new UnitConvertHelper();
        $this->_setLength($length)->_setWidth($width)->_setHeight($height)->_setWeight($weight);

        $this->_computeGirth();
        $this->_computeBulkWeight();
        $this->_computeGoodsTypeTag();

    }

    /**
     * 获取商品 tag
     * @return array
     */
    public function getGoodsTag(): array {
        return $this->tag;
    }

    /**
     * 获取商品实重 lb
     * @return int
     */
    private function _getRealWeightLB(): int {

        return (int)max($this->weight, $this->bulkWeight);
    }


    /**
     * 设置 长度
     * @param int $length 单位 cm
     * @return goods
     */
    private function _setLength(int $length): goods {
        if(!$this->_checkValidity($length)) {
            // TODO 异常处理
            new Exception('length is not valid', 1);
        }
        $this->length = ceil($this->unitConvertHelper->cm2in($length));
        return $this;
    }

    /**
     * 设置 宽度
     * @param int $width 单位 cm
     * @return goods
     */
    private function _setWidth(int $width): goods {
        if(!$this->_checkValidity($width)) {
            // TODO 异常处理
            new Exception('width is not valid', 2);
        }
        $this->width = ceil($this->unitConvertHelper->cm2in($width));
        return $this;
    }

    /**
     * 设置 高度
     * @param int $height 单位 cm
     * @return goods
     */
    private function _setHeight(int $height): goods {
        if(!$this->_checkValidity($height)) {
            // TODO 异常处理
            new Exception('height is not valid', 3);
        }
        $this->height = ceil($this->unitConvertHelper->cm2in($height));
        return $this;
    }

    /**
     * 设置 重量
     * @param int $weight 单位 kg
     * @return goods
     */
    private function _setWeight(int $weight): goods {
        if(!$this->_checkValidity($weight)) {
            // TODO 异常处理
            new Exception('height is not valid', 4);
        }
        $this->weight = ceil($this->unitConvertHelper->kg2lb($weight));
        return $this;
    }

    /**
     * 获取最长边长度 mm
     * @return mixed
     */
    private function _getLongestSide() {

        $arr = [$this->length, $this->width, $this->height];
        sort($arr, SORT_NUMERIC);

        return $arr[2];
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

        return $arr[0];
    }

    /**
     * 计算围长
     */
    private function _computeGirth() {
        // 单位转换为 inch
        $longestSide = $this->_getLongestSide(); // 最长边 inch
        $secondLongestSide = $this->_getSecondLongestSide(); // 次长边 inch
        $thirdLongestSide = $this->_getThirdSide(); // 次长边 inch

        $this->girth = ceil($longestSide + ($secondLongestSide + $thirdLongestSide) * 2);
    }

    /**
     * 计算体积重
     */
    private function _computeBulkWeight() {
        // 单位转换为 inch
        $longestSide = $this->_getLongestSide(); // 最长边 inch
        $secondLongestSide = $this->_getSecondLongestSide(); // 次长边 inch
        $thirdLongestSide = $this->_getThirdSide(); // 次长边 inch

        $this->bulkWeight = (int)ceil($longestSide * $secondLongestSide * $thirdLongestSide / self::BULK_WEIGHT_BASE);
    }

    private function _computeGoodsTypeTag() {

        $realWeight = $this->_getRealWeightLB(); // 实重 lb
        $girth = $this->girth; // 围长 inch
        $longestSide = $this->_getLongestSide(); // 最长边 inch
        $secondLongestSide = $this->_getSecondLongestSide(); // 次长边 inch

        switch (true) {
            case $realWeight > 150 || $longestSide > 108 :
                $this->tag[] = 'OUT_SPACE';
                break;
            case ($girth > 130 && $girth <= 165) || ($longestSide >= 96 && $longestSide < 108):
                $this->tag[] = 'OVERSIZE';
                break;
            default:
                if($realWeight > 50 && $realWeight <= 150) {
                    $this->tag[] = 'AHS-WEIGHT';
                }
                if($girth > 105 || $secondLongestSide >= 30 || ($longestSide >= 48 && $longestSide < 108)) {
                    $this->tag[] = 'AHS-SIZE';
                }
                break;
        }
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

        $goods = new Goods($length, $width, $height, $weight);

        return $goods->getGoodsTag();
    }
}

$obj = new Main();
$result = [];
$result[] = $obj->test(68, 70, 60, 23);
$result[] = $obj->test(114.50, 42, 26, 47.5);
$result[] = $obj->test(162, 60, 11, 14);
$result[] = $obj->test(113, 64, 42.5, 35.85);
$result[] = $obj->test(114.5, 17, 51.5, 16.5);

var_dump($result);