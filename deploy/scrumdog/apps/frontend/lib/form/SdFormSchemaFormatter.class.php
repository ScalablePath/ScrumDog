<?
class SdFormSchemaFormatter extends sfWidgetFormSchemaFormatter
{
  protected $rowFormat = "<tr>\n <td><nobr>%label%</nobr>\n</td><td>%error%%field%%help%%hidden_fields%</td>\n</tr>\n";
  protected $helpFormat = "<br /><span class=\"help\">%help%</span>";
  protected $errorRowFormat = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n";
  protected $errorListFormatInARow = " <ul class=\"error_list\">\n%errors% </ul>\n";
  protected $errorRowFormatInARow = " <li>%error%</li>\n";
  protected $namedErrorRowFormatInARow = " <li>%name%: %error%</li>\n";
}