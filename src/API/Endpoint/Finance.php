<?php

declare(strict_types=1);

namespace Dakword\WBSeller\API\Endpoint;

use Dakword\WBSeller\API\AbstractEndpoint;
use DateTime;
use InvalidArgumentException;

/**
 * ФИНАНСЫ
 */
class Finance extends AbstractEndpoint
{
    /**
     * Баланс продавца
     *
     * Возвращает данные виджета баланса на главной странице портала продавцов.
     * Максимум 1 запрос в 1 минуту
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Balans/paths/~1api~1v1~1account~1balance/get
     *
     * @return object {currency: string, current: float, for_withdraw: float}
     */
    public function balance(): object
    {
        return $this->getRequest('/api/v1/account/balance');
    }

    /**
     * Список отчётов реализации
     *
     * Данные доступны с 29 января 2024 года.
     * Максимум 1 запрос в 1 минуту
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty/paths/~1api~1finance~1v1~1sales-reports~1list/post
     *
     * @param DateTime $dateFrom Начальная дата отчёта
     * @param DateTime $dateTo   Конечная дата отчёта
     * @param int      $limit    Количество отчётов в ответе. Не более 1000
     * @param int      $offset   Сколько элементов пропустить
     * @param string   $period   Периодичность: weekly — еженедельные, daily — ежедневные
     *
     * @return array [ {object}, ... ]
     *
     * @throws InvalidArgumentException
     */
    public function salesReportsList(
        DateTime $dateFrom,
        DateTime $dateTo,
        int $limit = 1000,
        int $offset = 0,
        string $period = 'weekly'
    ): array {
        $maxLimit = 1_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых отчётов: {$maxLimit}");
        }
        if (!in_array($period, ['daily', 'weekly'], true)) {
            throw new InvalidArgumentException('Неизвестная периодичность отчётов: ' . $period);
        }

        return $this->postRequest('/api/finance/v1/sales-reports/list', [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'limit' => $limit,
            'offset' => $offset,
            'period' => $period,
        ]) ?? [];
    }

    /**
     * Детализации к отчётам реализации по ID отчёта
     *
     * Данные доступны с 29 января 2024 года.
     * Максимум 1 запрос в 1 минуту.
     * Для загрузки отчёта частями начинайте с rrdId = 0 и передавайте rrdId из последней строки
     * предыдущего ответа, пока не получите HTTP 204.
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty/paths/~1api~1finance~1v1~1sales-reports~1detailed~1%7BreportId%7D/post
     *
     * @param string|int $reportId ID отчёта
     * @param int        $limit    Количество строк в ответе. Не более 100000
     * @param int        $rrdId    ID строки ответа для постраничной загрузки
     * @param array      $fields   Список полей ответа. Если не указан — все поля
     *
     * @return array [ {object}, ... ]
     *
     * @throws InvalidArgumentException
     */
    public function salesReportsDetailedById($reportId, int $limit = 100_000, int $rrdId = 0, array $fields = []): array
    {
        $maxLimit = 100_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых строк отчёта: {$maxLimit}");
        }

        $body = [
            'limit' => $limit,
            'rrdId' => $rrdId,
        ];
        if ($fields) {
            $body['fields'] = array_values($fields);
        }

        return $this->postRequest('/api/finance/v1/sales-reports/detailed/' . $reportId, $body) ?? [];
    }

    /**
     * Детализации к отчётам реализации за период
     *
     * Данные доступны с 29 января 2024 года.
     * Максимум 1 запрос в 1 минуту.
     * Для загрузки отчёта частями начинайте с rrdId = 0 и передавайте rrdId из последней строки
     * предыдущего ответа, пока не получите HTTP 204.
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty/paths/~1api~1finance~1v1~1sales-reports~1detailed/post
     *
     * @param DateTime $dateFrom Начальная дата отчёта
     * @param DateTime $dateTo   Конечная дата отчёта
     * @param int      $limit    Количество строк в ответе. Не более 100000
     * @param int      $rrdId    ID строки ответа для постраничной загрузки
     * @param string   $period   Периодичность: weekly — еженедельные, daily — ежедневные
     * @param array    $fields   Список полей ответа. Если не указан — все поля
     *
     * @return array [ {object}, ... ]
     *
     * @throws InvalidArgumentException
     */
    public function salesReportsDetailed(
        DateTime $dateFrom,
        DateTime $dateTo,
        int $limit = 100_000,
        int $rrdId = 0,
        string $period = 'weekly',
        array $fields = []
    ): array {
        $maxLimit = 100_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых строк отчёта: {$maxLimit}");
        }
        if (!in_array($period, ['daily', 'weekly'], true)) {
            throw new InvalidArgumentException('Неизвестная периодичность отчётов: ' . $period);
        }

        $body = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'limit' => $limit,
            'rrdId' => $rrdId,
            'period' => $period,
        ];
        if ($fields) {
            $body['fields'] = array_values($fields);
        }

        return $this->postRequest('/api/finance/v1/sales-reports/detailed', $body) ?? [];
    }

    /**
     * Список отчётов об издержках на приём платежей
     *
     * Максимум 1 запрос в 1 минуту
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty/paths/~1api~1finance~1v1~1acquiring~1list/post
     *
     * @param DateTime $dateFrom Начальная дата отчёта
     * @param DateTime $dateTo   Конечная дата отчёта
     * @param int      $limit    Количество отчётов в ответе. Не более 1000
     * @param int      $offset   Сколько элементов пропустить
     *
     * @return array [ {object}, ... ]
     *
     * @throws InvalidArgumentException
     */
    public function acquiringList(
        DateTime $dateFrom,
        DateTime $dateTo,
        int $limit = 1000,
        int $offset = 0
    ): array {
        $maxLimit = 1_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых отчётов: {$maxLimit}");
        }

        return $this->postRequest('/api/finance/v1/acquiring/list', [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'limit' => $limit,
            'offset' => $offset,
        ]) ?? [];
    }

    /**
     * Детализации к отчётам об издержках на приём платежей по ID отчёта
     *
     * Максимум 1 запрос в 1 минуту.
     * Для загрузки отчёта частями начинайте с rrdId = 0 и передавайте rrdId из последней строки
     * предыдущего ответа, пока не получите HTTP 204.
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty/paths/~1api~1finance~1v1~1acquiring~1detailed~1%7BreportId%7D/post
     *
     * @param string|int $reportId ID отчёта
     * @param int        $limit    Количество строк в ответе. Не более 100000
     * @param int        $rrdId    ID строки ответа для постраничной загрузки
     * @param array      $fields   Список полей ответа. Если не указан — все поля
     *
     * @return array [ {object}, ... ]
     *
     * @throws InvalidArgumentException
     */
    public function acquiringDetailedById($reportId, int $limit = 100_000, int $rrdId = 0, array $fields = []): array
    {
        $maxLimit = 100_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых строк отчёта: {$maxLimit}");
        }

        $body = [
            'limit' => $limit,
            'rrdId' => $rrdId,
        ];
        if ($fields) {
            $body['fields'] = array_values($fields);
        }

        return $this->postRequest('/api/finance/v1/acquiring/detailed/' . $reportId, $body) ?? [];
    }

    /**
     * Детализации к отчётам об издержках на приём платежей за период
     *
     * Максимум 1 запрос в 1 минуту.
     * Для загрузки отчёта частями начинайте с rrdId = 0 и передавайте rrdId из последней строки
     * предыдущего ответа, пока не получите HTTP 204.
     *
     * @link https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty/paths/~1api~1finance~1v1~1acquiring~1detailed/post
     *
     * @param DateTime $dateFrom Начальная дата отчёта
     * @param DateTime $dateTo   Конечная дата отчёта
     * @param int      $limit    Количество строк в ответе. Не более 100000
     * @param int      $rrdId    ID строки ответа для постраничной загрузки
     * @param array    $fields   Список полей ответа. Если не указан — все поля
     *
     * @return array [ {object}, ... ]
     *
     * @throws InvalidArgumentException
     */
    public function acquiringDetailed(
        DateTime $dateFrom,
        DateTime $dateTo,
        int $limit = 100_000,
        int $rrdId = 0,
        array $fields = []
    ): array {
        $maxLimit = 100_000;
        if ($limit > $maxLimit) {
            throw new InvalidArgumentException("Превышение максимального количества запрашиваемых строк отчёта: {$maxLimit}");
        }

        $body = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'limit' => $limit,
            'rrdId' => $rrdId,
        ];
        if ($fields) {
            $body['fields'] = array_values($fields);
        }

        return $this->postRequest('/api/finance/v1/acquiring/detailed', $body) ?? [];
    }
}
