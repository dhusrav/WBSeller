<?php
declare(strict_types=1);

namespace Dakword\WBSeller\API\Endpoint;

use Dakword\WBSeller\API\AbstractEndpoint;
use InvalidArgumentException;

/**
 * ПОСТАВКИ
 */
class Supplies extends AbstractEndpoint
{
    /**
     * Коэффициенты приёмки
     *
     * Возвращает коэффициенты приёмки для конкретных складов на ближайшие 14 дней.
     * Приёмка для поставки доступна только при сочетании: coefficient = 0/1 и allowUnload = true
     * @link https://openapi.wb.ru/supplies/api/ru/#tag/Informaciya-dlya-formirovaniya-postavok/paths/~1api~1v1~1acceptance~1coefficients/get
     *
     * @param array $warehouses ID складов. Если параметр не указан, возвращаются данные по всем складам
     */
    public function coefficients(array $warehouses = [])
    {
        return $this->getRequest('/api/v1/acceptance/coefficients', $warehouses ? [
            'warehouseIDs' => implode(',', $warehouses),
        ] : []);
    }

    /**
     * Опции приёмки
     *
     * Возвращает информацию о том, какие склады и типы упаковки доступны для поставки.
     * Список складов определяется по баркоду товара и его количеству.
     * Максимум 30 запросов в минуту
     * @link https://openapi.wb.ru/supplies/api/ru/#tag/Informaciya-dlya-formirovaniya-postavok/paths/~1api~1v1~1acceptance~1options/post
     *
     * @param array    $items       Массив с товарами и планируемыми количествами для поставки
     * @param int|null $warehouseId ID склада. Если параметр не указан, возвращаются данные по всем складам.
     *
     * @throws InvalidArgumentException Превышение максимального количества запрашиваемых товаров
     */
    public function options(array $items, ?int $warehouseId = null)
    {
        $maxLimit = 5_000;
        if (count($items) > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых товаров: {$maxLimit}");
        }
        return $this->postRequest('/api/v1/acceptance/options' . ($warehouseId ? ("?warehouseID={$warehouseId}"): ''), $items);
    }

    /**
     * Список складов
     *
     * Возвращает список складов Wildberries.
     * Максимум 6 запросов в минуту
     * @link https://openapi.wb.ru/supplies/api/ru/#tag/Informaciya-dlya-formirovaniya-postavok/paths/~1api~1v1~1warehouses/get
     */
    public function warehouses()
    {
        return $this->getRequest('/api/v1/warehouses');
    }

    /**
     * Список поставок
     *
     * Возвращает список поставок Wildberries.
     * Максимум 30 запросов в минуту, интервал 2 секунды
     * @link https://dev.wildberries.ru/openapi/orders-fbw#tag/Informaciya-o-postavkah/paths/~1api~1v1~1supplies/post
     */
    public function getSupplies(array $dates = [], ?int $limit = 1000, ?int $offset = 0, ?array $statusIDs = [1,2,3,4,5,6])
    {

        $maxLimit = 1_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых товаров: {$maxLimit}");
        }

        return $this->postRequest('/api/v1/supplies?' . http_build_query([
                'limit' => $limit,
                'offset' => 0,
            ]),
            ['dates' => $dates, 'statusIDs' => $statusIDs]);
    }

    /**
     * Детали поставки
     *
     * Возвращает детальную информацию о поставке Wildberries.
     * Максимум 30 запросов в минуту, интервал 2 секунды
     * @link https://dev.wildberries.ru/openapi/orders-fbw#tag/Informaciya-o-postavkah/paths/~1api~1v1~1supplies~1%7BID%7D/get
     */
    public function getSupplyDetail(int $supplyId)
    {
        return $this->getRequest('/api/v1/supplies/' . $supplyId);
    }
}
