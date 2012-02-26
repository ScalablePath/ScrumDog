<?
class ModernFormSchemaFormatter extends sfWidgetFormSchemaFormatter
{
  protected $rowFormat = "<div class=\"item\">\n%label%\n<div class=\"field\">%error%%field%%help%%hidden_fields%</div>\n</div>\n";
  protected $helpFormat = "<br /><span class=\"help\">%help%</span>";
  protected $errorRowFormat = "<div class=\"item\">\n%errors%</div>\n";
  protected $errorListFormatInARow = " <ul class=\"error_list\">\n%errors% </ul>\n";
  protected $errorRowFormatInARow = " <li>%error%</li>\n";
  protected $namedErrorRowFormatInARow = " <li>%name%: %error%</li>\n";
}