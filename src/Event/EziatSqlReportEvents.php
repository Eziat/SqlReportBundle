<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Event;

/**
 * Contains all events thrown in the Sql report bundle.
 *
 * @author Tomas
 */
final class EziatSqlReportEvents
{
    const SQL_REPORT_EDIT_INITIALIZE = 'eziat_sql_report.sql_report.edit.initialize';

    const SQL_REPORT_EDIT_SUCCESS    = 'eziat_sql_report.sql_report.edit.success';

    const SQL_REPORT_EDIT_COMPLETE   = 'eziat_sql_report.sql_report.edit.complete';

    const SQL_REPORT_LIST_INITIALIZE = 'eziat_sql_report.sql_report.list.initialize';

    const SQL_REPORT_SHOW_INITIALIZE = 'eziat_sql_report.sql_report.show.initialize';

    const SQL_REPORT_PDF_INITIALIZE = 'eziat_sql_report.sql_report.pdf.initialize';

    const SQL_REPORT_EXPORT_INITIALIZE = 'eziat_sql_report.sql_report.export.initialize';
}