<?php
/**
 * Created by PhpStorm.
 * User: dmitrij
 * Date: 18/09/2019
 * Time: 23:27
 */

use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Loader;

/**
 * Class GetListCached
 */
class GetListCached {
    /**
     * Кешированный метод получения списка элементов инфоблока
     * На мой взгляд подобного рода метод может в реальной разработке даже несколько навредить по причине разростания кеша
     * при многократном использовании (так как на кеширование влияют передаваемые параметры)
     * @param array $filter - параметры фильтрации
     * @param array $select - параметры выборки (по умолчанию все поля)
     * @param array $order - параметры сортровки (по умолчанию SORT)
     * @return object | bool - CIBlockResult или false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function GetList($order=["SORT" => "ASC"], $filter=[], $select=[]) {
        if (!Loader::includeModule('iblock')) {
            return false;
        }

        // параметры кеша
        $arParams['CACHE_TIME'] = 3600;
        $arParams['CACHE_DIR'] = "/getElements";

        // формируем уникальный ключ
        $cache_id = md5(serialize(array($order, $filter, $select)));
        $obCache = Cache::createInstance();

        if($obCache->InitCache($arParams['CACHE_TIME'], $cache_id, $arParams['CACHE_DIR'])) {
            $elements = $obCache->GetVars()['RESULT'];
        } else {
            $elements =  \CIBlockElement::GetList ($order, $filter, false, false, $select);

            $result = array();
            while($element = $elements->fetch()) {
                $result[] = $element;
            }

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache([
                    "RESULT" => $result
                ]);
            }
        }

        return $elements;
    }
}

// TODO Возможные улучшения:

// Можно использовать методы тегированного кеша, для возможности отслеживания изменения данных со стороны админки
// но у нас небольшое время кеширования, поэтому нет необходимости

// Можно было использовать методы D7 по работе с элементами, но там лишние сложности со свойствами элементов
// с другой стороны это дало бы прирост к скорости на этапе выборки

$arFilter = ['IBLOCK_ID' => 3];
$arSelect = ['NAME'];
var_dump(GetListCached::GetList(array(), $arFilter, $arSelect));




