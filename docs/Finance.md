## [WBSeller API](/docs/API.md) / Finance()

```php
$wbSellerAPI = new \Dakword\WBSeller\API($options);
$Finance = $wbSellerAPI->Finance();
```

Wildberries API / [**Документы и бухгалтерия**](https://dev.wildberries.ru/docs/openapi/documents-and-accounting)

| :speech_balloon: | :cloud: | [Finance()](/src/API/Endpoint/Finance.php) |
| ---------------- | ------- | ------------------------------------------ |
| Проверка подключения к API | /ping | Finance()->**ping()** |
| [**Баланс**](https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Balans) |||
| Баланс продавца | /api/v1/account/balance | Finance()->**balance()** |
| [**Финансовые отчёты**](https://dev.wildberries.ru/docs/openapi/documents-and-accounting#tag/Finansovye-otchyoty) |||
| Список отчётов реализации | /api/finance/v1/sales-reports/list | Finance()->**salesReportsList()** |
| Детализации к отчётам реализации по ID | /api/finance/v1/sales-reports/detailed/{reportId} | Finance()->**salesReportsDetailedById()** |
| Детализации к отчётам реализации за период | /api/finance/v1/sales-reports/detailed | Finance()->**salesReportsDetailed()** |
| Список отчётов об издержках на приём платежей | /api/finance/v1/acquiring/list | Finance()->**acquiringList()** |
| Детализации к отчётам эквайринга по ID | /api/finance/v1/acquiring/detailed/{reportId} | Finance()->**acquiringDetailedById()** |
| Детализации к отчётам эквайринга за период | /api/finance/v1/acquiring/detailed | Finance()->**acquiringDetailed()** |
