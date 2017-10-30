# admin-rank-statistic

Site ranking di Alexa dan Similarweb dari panel admin.

Pastikan menambahkan cron dengan syntax seperti dibawah agar data rank selalu terupdate:

```
0 */3 * * * curl HOST/admin/statistic/rank/update
```