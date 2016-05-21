<?php
namespace SynergyCommon;

use Gedmo\Sluggable\Util\Urlizer;
use SynergyCommon\Util as CommonUtil;
use Zend\Console\Request as ConsoleRequest;
use Zend\Filter\FilterChain;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Validator\Uri;

/**
 * Class Util
 *
 * @package SynergyCommon
 */
class Util
{

    const DEFAULT_LOCALE    = 'en_GB';
    const DB_DATE_FORMAT    = 'Y-m-d H:i:s';
    const CLIENT_DOMAIN_KEY = 'client_domain';

    protected static $_enablePrint = false;

    protected static $_mimeTypes
        = array(
            "ez"      => "application/andrew-inset",
            "hqx"     => "application/mac-binhex40",
            "cpt"     => "application/mac-compactpro",
            "doc"     => "application/msword",
            "bin"     => "application/octet-stream",
            "dms"     => "application/octet-stream",
            "lha"     => "application/octet-stream",
            "lzh"     => "application/octet-stream",
            "exe"     => "application/octet-stream",
            "class"   => "application/octet-stream",
            "so"      => "application/octet-stream",
            "dll"     => "application/octet-stream",
            "oda"     => "application/oda",
            "pdf"     => "application/pdf",
            "ai"      => "application/postscript",
            "eps"     => "application/postscript",
            "ps"      => "application/postscript",
            "smi"     => "application/smil",
            "smil"    => "application/smil",
            "wbxml"   => "application/vnd.wap.wbxml",
            "wmlc"    => "application/vnd.wap.wmlc",
            "wmlsc"   => "application/vnd.wap.wmlscriptc",
            "bcpio"   => "application/x-bcpio",
            "vcd"     => "application/x-cdlink",
            "pgn"     => "application/x-chess-pgn",
            "cpio"    => "application/x-cpio",
            "csh"     => "application/x-csh",
            "dcr"     => "application/x-director",
            "dir"     => "application/x-director",
            "dxr"     => "application/x-director",
            "dvi"     => "application/x-dvi",
            "spl"     => "application/x-futuresplash",
            "gtar"    => "application/x-gtar",
            "hdf"     => "application/x-hdf",
            "js"      => "application/x-javascript",
            "skp"     => "application/x-koan",
            "skd"     => "application/x-koan",
            "skt"     => "application/x-koan",
            "skm"     => "application/x-koan",
            "latex"   => "application/x-latex",
            "nc"      => "application/x-netcdf",
            "cdf"     => "application/x-netcdf",
            "sh"      => "application/x-sh",
            "shar"    => "application/x-shar",
            "swf"     => "application/x-shockwave-flash",
            "sit"     => "application/x-stuffit",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc"  => "application/x-sv4crc",
            "tar"     => "application/x-tar",
            "tcl"     => "application/x-tcl",
            "tex"     => "application/x-tex",
            "texinfo" => "application/x-texinfo",
            "texi"    => "application/x-texinfo",
            "t"       => "application/x-troff",
            "tr"      => "application/x-troff",
            "roff"    => "application/x-troff",
            "man"     => "application/x-troff-man",
            "me"      => "application/x-troff-me",
            "ms"      => "application/x-troff-ms",
            "ustar"   => "application/x-ustar",
            "src"     => "application/x-wais-source",
            "xhtml"   => "application/xhtml+xml",
            "xht"     => "application/xhtml+xml",
            "zip"     => "application/zip",
            "au"      => "audio/basic",
            "snd"     => "audio/basic",
            "mid"     => "audio/midi",
            "midi"    => "audio/midi",
            "kar"     => "audio/midi",
            "mpga"    => "audio/mpeg",
            "mp2"     => "audio/mpeg",
            "mp3"     => "audio/mpeg",
            "aif"     => "audio/x-aiff",
            "aiff"    => "audio/x-aiff",
            "aifc"    => "audio/x-aiff",
            "m3u"     => "audio/x-mpegurl",
            "ram"     => "audio/x-pn-realaudio",
            "rm"      => "audio/x-pn-realaudio",
            "rpm"     => "audio/x-pn-realaudio-plugin",
            "ra"      => "audio/x-realaudio",
            "wav"     => "audio/x-wav",
            "pdb"     => "chemical/x-pdb",
            "xyz"     => "chemical/x-xyz",
            "bmp"     => "image/bmp",
            "gif"     => "image/gif",
            "ief"     => "image/ief",
            "jpeg"    => "image/jpeg",
            "jpg"     => "image/jpeg",
            "jpe"     => "image/jpeg",
            "png"     => "image/png",
            "tiff"    => "image/tiff",
            "tif"     => "image/tif",
            "djvu"    => "image/vnd.djvu",
            "djv"     => "image/vnd.djvu",
            "wbmp"    => "image/vnd.wap.wbmp",
            "ras"     => "image/x-cmu-raster",
            "pnm"     => "image/x-portable-anymap",
            "pbm"     => "image/x-portable-bitmap",
            "pgm"     => "image/x-portable-graymap",
            "ppm"     => "image/x-portable-pixmap",
            "rgb"     => "image/x-rgb",
            "xbm"     => "image/x-xbitmap",
            "xpm"     => "image/x-xpixmap",
            "xwd"     => "image/x-windowdump",
            "igs"     => "model/iges",
            "iges"    => "model/iges",
            "msh"     => "model/mesh",
            "mesh"    => "model/mesh",
            "silo"    => "model/mesh",
            "wrl"     => "model/vrml",
            "vrml"    => "model/vrml",
            "css"     => "text/css",
            "csv"     => "text/csv",
            "html"    => "text/html",
            "htm"     => "text/html",
            "txt"     => "text/plain",
            "asc"     => "text/plain",
            "rtx"     => "text/richtext",
            "rtf"     => "text/rtf",
            "sgml"    => "text/sgml",
            "sgm"     => "text/sgml",
            "tsv"     => "text/tab-seperated-values",
            "wml"     => "text/vnd.wap.wml",
            "wmls"    => "text/vnd.wap.wmlscript",
            "etx"     => "text/x-setext",
            "xml"     => "text/xml",
            "xsl"     => "text/xml",
            "mpeg"    => "video/mpeg",
            "mpg"     => "video/mpeg",
            "mpe"     => "video/mpeg",
            "qt"      => "video/quicktime",
            "mov"     => "video/quicktime",
            "mxu"     => "video/vnd.mpegurl",
            "avi"     => "video/x-msvideo",
            "movie"   => "video/x-sgi-movie",
            "ice"     => "x-conference-xcooltalk"
        );

    /**
     * Ensure string is returned
     *
     * @param $value
     *
     * @return array|string
     */
    public static function ensureIsString($value)
    {
        if (is_object($value) or is_null($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return $value ? 1 : 0;
        } elseif (is_array($value)) {
            return implode(', ', array_filter($value));
        } else {
            return trim(self::removeLineBreaks((string)$value), '!');
        }
    }

    public static function removeLineBreaks($text, $replacement = ' ')
    {
        if (is_string($text)) {
            //$data = \preg_replace('#[\r\n]+#', $replacement, $text);
            $data = \preg_replace('/[\p{C}\p{Z}]+/iu', $replacement, $text);
            $data = \preg_replace('#\s+#', $replacement, $data);
            $data = str_replace(['<br>', '<br />'], '', $data);

            return trim($data);
        }

        return '';
    }

    /**
     * @param $object
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function customCall($object, $method, $args)
    {

        $numArgs = count($args);

        switch ($numArgs) {
            case 0:
                return $object->$method();
            case 1:
                return $object->$method($args[0]);
            case 2:
                return $object->$method($args[0], $args[1]);
            case 3:
                return $object->$method($args[0], $args[1], $args[3]);
            case 4:
                return $object->$method($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4]);
            case 6:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            case 7:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
            default:
                return call_user_func_array(array($object, $method), $args);
        }
    }

    private static function getFirstPartFromString($original, array $list)
    {
        $text = mb_convert_encoding((string)$original, 'UTF-8', mb_list_encodings());
        $text = mb_convert_case($text, MB_CASE_TITLE, "UTF-8");

        foreach ($list as $sep) {
            $paths = explode($sep, $text);
            $text  = trim($paths[0]);
        }

        return $text;
    }

    public static function prepareTitleForSlug($text, $unAccent = true, $firstPart = true)
    {
        $original = $text;

        if ('Купистол' == $original) {
            $text = 'Kupistol';
        }
        if ($firstPart) {
            $sepList    = [' - ', '- ', ' -', ' – ', '(', '[', '<>', '_', ' - ', '*', ':', '|', ' - '];
            $separators = [
                'closing',
                'via',
                'loja',
                'ihr',
                '.com-'
            ];
            $regex      = '/\b(?:' . implode('|', $separators) . ')\b/i';
            $text       = preg_replace($regex, '<>', $text);
            $text       = self::getFirstPartFromString($text, $sepList);
        }

        $text = str_ireplace(
            array(
                "'s",
                'hoteles',
                'Hoteles',
                ' and ',
                ' And ',
                ' AND ',
            ),
            array(
                '',
                'hotels',
                'Hotels',
                ' & ',
                ' & ',
                ' & ',
            ),
            $text
        );

        $text = str_ireplace(
            array(
                '(US & CA)',
                'US  CA',
                'US CA',
                '(US & amp; CA)',
                '(us)',
                '(usa)',
                '.com',
                '.co.uk',
                '.org',
                ' &#39;',
                '.pl',
                '.de',
                'GMBH',
                'llc',
                'LLC',
                '.ca',
                '.ch',
                '.at',
                '.it',
                '.se',
                '.es',
                '.hu',
                '.no',
                '.fr',
                '.nl',
                '.be',
                '.ie',
                '.tr',
                '.au',
                '.sk',
                '.cz',
                '.nz',
                '.biz',
                '.us',
                'gmbh',
                'Vouchers',
                'Affiliate Programme',
                'Affiliate Program',
                'Programme',
                'programme',
                'Program',
                'programs',
                '.lt',
                '.fi',
                '.in',
                '.net',
                '.dk',
                'Affiliate Program',
                'Partnerprogramm',
                'affiliation',
                'ireland',
                'Programmes',
                'Ireland',
                'International',
                'Poland',
                'Onlineshop',
                'Schuhe',
                'Program',
                'Star Program',
                'Deutschland',
                'Schweiz',
                'Closed',
                'www.',
                '.cz',
                'http://',
                '.pt',
                'Fiesta',
                'affiliate',
                'Affiliate',
                '(Global)',
                '(AU)',
                '(NZ)',
                'Inc.',
                'Co.',
                'AFFILIATE LINK TRACKING',
                'Couponeur',
                'Couponeurs',
                'Couponneurs',
                'Bons de réduction',
                'Gutscheinpartner',
                'Gutschein Partner',
                'Cupón',
                'Coupons et Cashback',
                'Bon Plan',
                'Referral',
                'Cashback',
                '(Public)',
                'BEFR',
                'Partnerm',
                'BeFR',
                'Be NL',
                'Benl',
                'Befr',
                'Intl.',
                'FRBE',
                'Belgium',
                'Campaign',
                'Mexico',
                'Portugal',
                'APK',
                'Android',
                'CPI',
                'CPL',
                'CPS',
                'Cps',
                'Cpa',
                'CPA',
                'CPR',
                'CPV',
                'CPI',
                'U.S.',
                '.comcz',
                'Mac only',
                'Per Sale',
                'Nederland',
                'Sweden',
                'Polska',
                '*suspended*',
                'suspended',
                'Scandinavia',
                'Nigeria',
                'Italia',
                'Hungary',
                'DACH',
                'Affiliate Team',
                'Gutschein Gewinnspiel',
                'Gutschein',
                'Gewinnspiel',
                'Gewinnspiele',
                'oesterreich',
                'Oesterreich',
                'Österreich',
            ),
            '',
            $text
        );

        if (preg_match('/(tchibo|art2chine|armordirect|deichmann|sercotel)\-[\d]+$/i', $text, $match)) {
            $text = $match[1];
        }
        $text = html_entity_decode($text, \ENT_QUOTES, 'utf-8');
        $text = str_replace('_', ' ', $text);
        if ($unAccent) {
            $text = Urlizer::unaccent($text);
        }
        $filter = new FilterChain(
            array(
                'filters' => array(
                    //array('name' => 'stringToLower', 'options' => array('encoding' => 'utf-8')),
                    array('name' => 'stripTags'),
                    array(
                        'name'    => 'pregReplace',
                        'options' => array(
                            'pattern'     => '/\.(com|co\.uk)$/i',
                            'replacement' => '',
                        ),
                    ),
                    array(
                        'name'    => 'pregReplace',
                        'options' => array(
                            'pattern'     => '/\b(int|nl\/be|nl\/de|esp|pt|AR|AUS|llc|codes|dhs|gb|Smb|\(.*\)|\[.*\]|ireland|payg|contracts|gmbh|eu|and|limited|ltd|plc|\.co\.|uk|inc|hu|ch|fr|es|nz|dk|se|ru|br|cn|jp|no|ca|ie|tr|au|lt|fi|other|dach|-uk|[^a-z0-9\-\_\s])\b/i',
                            'replacement' => '',
                        ),
                    ),
                    array(
                        'name'    => 'pregReplace',
                        'options' => array(
                            'pattern'     => '/\s+(?:at|it|cz|sk|rus|us|eu|global|cpl|en|apac|ch|de|be|nl|australia|austria|canada|at|pl|es|global|sk|sg|tw|hk|usa|android|pvt)$/i',
                            'replacement' => '',
                        ),
                    ),

                    array(
                        'name'    => 'pregReplace',
                        'options' => array(
                            'pattern'     => '/([^\p{L}\p{N}\-\_\s\.]+\')$/iu',
                            'replacement' => ''
                        ),
                    ),
                    array('name' => 'stringTrim'),
                ),
            )
        );

        $name = trim($filter->filter($text));
        $name = trim($name, '-., :');

        switch (mb_strtolower($name, 'UTF-8')) {
            case 'host europe':
                $name = 'Host Europe';
                break;
            case 'from':
                $name = 'From US';
                break;
            case 'hut':
                $name = 'The Hut';
                break;
            case 'toysr':
            case 'toys r':
                $name = 'Toys R US';
                break;
            case 'babies r':
                $name = 'Babies R US';
                break;
            case 'miniinthebox':
            case 'mini the box':
            case 'mini box':
                $name = 'Mini in the Box';
                break;
            case 'light the box':
            case 'light box':
                $name = 'Light in the Box';
                break;
            case 'blue':
            case 'shoes':
            case 'vision direct':
                $name = $original;
                break;
            case 'dx':
                $name = 'deal-extreme';
                break;
        }

        if (empty($name)) {
            $name = $original;
        }

        if (strlen(str_replace('-', '', $name)) < 3) {
            $name = trim($original);
        }

        $camelFilter = new CamelCaseToSeparator(' ');
        $name        = $camelFilter->filter($name);
        $name        = trim($name, ' &-!,._');

        return $name;
    }

    /**
     * @param       $data
     * @param array $options
     * @param bool $firstPart
     *
     * @return string
     */
    public static function urlize($data, $options = array(), $firstPart = true)
    {
        $str = self::prepareTitleForSlug($data, true, $firstPart);
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter'     => '-',
            'limit'         => null,
            'lowercase'     => true,
            'replacements'  => array(),
            'transliterate' => false,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = array(
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    public static function mimeTypeToExtension($mimeType)
    {
        return array_search($mimeType, self::$_mimeTypes) ?: 'txt';
    }

    public static function humanFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        /** @var $factor int */
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function printMessage($msg, $repeat = 1, $lineBreak = true, $forceOutput = false)
    {
        if (self::$_enablePrint or $forceOutput) {
            $msg  = is_array($msg) ? print_r($msg, true) : $msg;
            $sign = $repeat ? str_repeat("\t", $repeat) . ' ' : '';
            if ($lineBreak) {
                echo "{$sign}$msg\n";
            } else {
                echo "{$sign}$msg";
            }
        }
    }

    public static function setEnablePrint($enablePrint)
    {
        self::$_enablePrint = $enablePrint;
    }

    public static function getEnablePrint()
    {
        return self::$_enablePrint;
    }

    /**
     * Gets the column headers from the feed. It is assumed  that the headers are
     * in the first row of the file
     *
     * @param        $filename
     * @param string $separator
     * @param int $offSet
     * @param bool $sort
     *
     * @return array
     */
    public static function getFeedColumns($filename, $separator = ',', $offSet = 0, $sort = true)
    {
        $columns = array();
        if (file_exists($filename)) {
            $handle = fopen($filename, 'r');
            if ($offSet == 0) {
                $columns = fgetcsv($handle, null, $separator);
            } else {
                for ($i = 0; $i <= $offSet; $i++) {
                    $columns = fgetcsv($handle, null, $separator);
                    if ($i > $offSet) {
                        break;
                    }
                }
            }
            fclose($handle);
        }

        if ($sort) {
            sort($columns);

            return array_filter($columns);
        }

        return $columns;
    }

    /**
     * @param $routeName
     * @param $uniqueId
     *
     * @return string
     */
    public static function getResourceString($routeName, $uniqueId)
    {
        return 'mvc:' . strtolower($routeName) . '.' . strtolower($uniqueId);
    }

    /**
     * @param $date
     *
     * @return bool|string
     */
    public static function formatToUTC($date)
    {
        $date = ($date instanceof \DateTime) ? $date->getTimestamp() : $date;
        // Get the default timezone
        $default_tz = date_default_timezone_get();
        // Set timezone to UTC
        date_default_timezone_set("UTC");
        // convert datetime into UTC
        $utc_format = date("Y-m-d\\TH:i:s\\Z", $date);
        // Might not need to set back to the default but did just in case
        date_default_timezone_set($default_tz);

        return $utc_format;
    }

    /**
     * Determines if the date is expired
     *
     * @param $end
     *
     * @return bool
     */
    public static function isExpired($end)
    {
        if ($end) {
            try {
                $now     = new \DateTime();
                $endDate = ($end instanceof \DateTime) ? $end : new \DateTime($end);

                return ($now > $endDate);
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Deters memcached version
     *
     * @return int
     */
    public static function getMemcachedVersion()
    {
        $version = (string)phpversion('memcached');

        return ($version !== '') ? (int)$version[0] : 0;
    }

    public static function isMemcacheAvailable()
    {
        return version_compare('2.0.0', phpversion('memcache')) <= 0;
    }

    /**
     * Converts XML to Array
     *
     * @param $file
     *
     * @return array
     */
    public static function XmlArray($file)
    {
        $string = file_get_contents($file);
        $xml    = simplexml_load_string($string);
        $json   = json_encode($xml);

        $array = json_decode($json, true);

        return $array;
    }

    /**
     * @param string $logoPath
     *
     * @return array
     */
    public static function getLogoInventory($logoPath = 'data/logo-inventory.txt')
    {
        $list = array();

        if (self::isFileExpired($logoPath, 4)) {
            $destination = getcwd() . '/' . $logoPath;
            $remotePath  = '/var/www/vhost/static/assets/merchants/compressed/png/';
            $remoteUser  = 'live@37.61.202.70';

            $command = sprintf(
                'ssh %s ls %s > %s',
                $remoteUser,
                escapeshellarg($remotePath),
                escapeshellarg($destination)
            );

            exec($command);
        }

        if (file_exists($logoPath)) {
            $handle = fopen($logoPath, 'r');
            $count  = 0;
            while ($logo = fgets($handle)) {
                if ($logo) {
                    $list[trim($logo)] = $count;
                    $count++;
                }
            }
        }

        return $list;
    }

    public static function cleanUrl($url)
    {
        $validator = new Uri();
        if (!$validator->isValid($url)) {
            return '';
        }

        if (self::isTrackingDomain($url)) {
            return '';
        }
        $paths = parse_url($url);
        if (isset($paths['host'])) {
            $path  = isset($paths['path']) ? $paths['path'] : '';
            $clean = sprintf('%s://%s/%s', $paths['scheme'], $paths['host'], ltrim($path, '/'));

            return rtrim($clean, '/');
        }

        return '';
    }

    public static function isTrackingDomain($url)
    {
        $invalidDomain = [
            'track.condatix.de',
            'kl.adspirit.de',
            'tycoonpartner.adspirit.net',
            'bit.ly',

        ];
        foreach ($invalidDomain as $dom) {
            if (stripos($url, $dom) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function getMerchantLinkFromDeepLink($deepLink)
    {

        if (stripos($deepLink, 'tradedoubler') !== false) {
            $paths    = explode('url(', $deepLink);
            $deepLink = Util::removeLineBreaks($paths[0]);
        } elseif (stripos($deepLink, 'http://ad.zanox.com') !== false) {
            $parts = explode('&', $deepLink);
            foreach ($parts as $key => $part) {
                if (strpos($part, 'ULP') === 0) {
                    unset($parts[$key]);
                }
            }

            $deepLink = implode('&', $parts);
        } elseif (stripos($deepLink, 'tracking.mailsectkr.com') !== false) {
            $paths  = parse_url($deepLink);
            $params = urldecode($paths['query']);
            parse_str($params, $query);
            unset($query['url']);

            return sprintf('%s://%s%s?%s', $paths['scheme'], $paths['host'], $paths['path'], http_build_query($query));
        } elseif (stripos($deepLink, 'track.webgains.com') !== false) {
            $paths  = parse_url($deepLink);
            $params = urldecode($paths['query']);
            parse_str($params, $query);
            unset($query['wgtarget']);
            unset($query['utm_source']);
            unset($query['utm_medium']);
            unset($query['utm_campaign']);
            unset($query['utm_content']);
            unset($query['wmid']);
            unset($query['nvc']);
            unset($query['ord']);

            $queryString = '';
            if (!empty($query)) {
                $queryString = '?' . http_build_query($query);
            }

            return sprintf('%s://%s%s%s', $paths['scheme'], $paths['host'], $paths['path'], $queryString);
        }

        return self::removeLineBreaks($deepLink);
    }

    public static function isFileExpired($filename, $hours = 72)
    {
        $cutOff = time() - (60 * 60 * $hours);
        if (is_readable($filename) and filemtime($filename) > $cutOff) {
            return false;
        }

        return true;
    }

    /**
     * @param $domain
     *
     * @return mixed
     */
    public static function cleanDomain($domain)
    {
        //remove port number

        list($domain,) = explode(':', $domain);

        return str_replace(
            array(
                'http://',
                'https://',
                'www.',
                'admin.'
            ),
            '',
            $domain
        );
    }

    /**
     * @param $domain
     * @param $isSubDomain
     *
     * @return string
     */
    public static function formatDomain($domain, $isSubDomain)
    {
        if ($isSubDomain) {
            return 'http://' . rtrim($domain, '/');
        } else {
            return 'http://www.' . rtrim($domain, '/');
        }
    }

    /**
     * @param $source
     * @param $destination
     *
     * @return string
     */
    public static function curlDownload($source, $destination)
    {
        if ($source and $destination) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $source);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            $data  = curl_exec($ch);
            $error = curl_error($ch);

            curl_close($ch);
            if ($data) {
                $file = fopen($destination, "w+");
                $size = fputs($file, $data);
                fclose($file);

                return $size;
            }

            return $error;
        }

        return false;
    }

    /**
     * converts XML to array
     *
     * @param $xmlstring
     *
     * @return mixed
     */
    public static function xml2Array($xmlstring)
    {
        $array = [];
        if ($xmlstring) {
            $xml   = simplexml_load_string($xmlstring, null, LIBXML_NOCDATA);
            $json  = json_encode($xml);
            $array = json_decode($json, true);
        }

        return (array)$array;
    }

    public static function isValidUrl($url)
    {
        // first do some quick sanity checks:
        if (!$url || !is_string($url)) {
            return false;
        }

        if (strlen($url) > 150) {
            return false;
        }
        // quick check url is roughly a valid http request: ( http://blah/... )
        if (!preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
            return false;
        }

        $parts = parse_url($url);
        $url   = $parts['scheme'] . '://' . $parts['host'];
        // the next bit could be slow:
        //if (self::getHttpResponseCode_using_curl($url) != 200) {
        if (self::getHttpResponseCodeUsingGetheaders($url) != 200) {  // use this one if you cant use curl
            return false;
        }

        return $url;
    }

    public static function isValidDeepLink($url)
    {
        // first do some quick sanity checks:
        if (!$url || !is_string($url)) {
            return false;
        }

        $validator = new Uri();
        if (!$isValid = $validator->isValid($url)) {
            return false;
        }
        // quick check url is roughly a valid http request: ( http://blah/... )
        /*   if (!preg_match('/^http(s)?:\/\/[a-z0-9-\(\)\_]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
               return false;
           }
   */
        // the next bit could be slow:
        //if (self::getHttpResponseCode_using_curl($url) != 200) {
        if (!$data = self::getHttpResponseUsingCurl($url)) {
            // use this one if you cant use curl
            return false;
        } else {
            list($code, $body) = $data;

            if ($code == 403) {
                return false;
            }

            if ($code !== 200) {
                if (!(self::getHttpResponseCodeUsingCurl($url, false) == 302 and $body)) {
                    return false;
                }
            } elseif (stripos($body, '<html') === false && stripos($body, 'DOCTYPE html') === false) { //
                return false;
            }

            if ($body) {
                $texts = [
                    '<title>Webgains</title>',
                    'You’ve been redirected to this page because the link you clicked is now inactive',
                    'no relationship',
                    'advertiser is not active',
                    'link is incorrect',
                    'now inactive',
                    'not permitted',
                    'contact the webmaster',
                    'have permission to access',
                    'offer could not be found',
                    'campaign has been deactivated',
                    'this link is not active',
                    'This page cannot be found',
                    'this shop is currently unavailable',
                    'Welcome to TradeTracker',
                    'product is no longer available via this affiliate link',
                    'Campaign is not Active',
                    'Unfortunately we cannot redirect you to the website requested',
                    'www.voucherhive.co.uk', //redirected back
                    'Начните экономить',
                    'the web page I was looking for is no longer available',
                    'the page you are looking for no longer exists',
                    'Unfortunately, the link that you’ve used is not valid',
                ];

                $sourceHost = str_replace(['www.'], '', parse_url($url, PHP_URL_HOST));
                foreach ($texts as $text) {
                    if (stripos($body, $text) !== false) {
                        return false;
                    }
                }

                if ($hiddenRedirectUrl = self::getHiddenRedirectUrl($body)) {
                    $destHost = str_replace(['www.'], '', parse_url($hiddenRedirectUrl, PHP_URL_HOST));
                    if ($destHost != $sourceHost) {
                        return self:: isValidDeepLink($hiddenRedirectUrl);
                    }
                }
            }
        }

        // }

        return $url;
    }

    public static function getHttpResponseCodeUsingCurl($url, $followredirects = true)
    {
        // returns int responsecode, or false (if url does not exist or connection timeout occurs)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if (!$url || !is_string($url)) {
            return false;
        }
        $ch = @curl_init($url);
        if ($ch === false) {
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        @curl_setopt($ch, CURLOPT_NOBODY, true);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // catch output (do NOT print!)
        if ($followredirects) {
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt(
                $ch, CURLOPT_MAXREDIRS, 10
            );  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        } else {
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        }
        @curl_setopt(
            $ch, CURLOPT_CONNECTTIMEOUT, 10
        );   // fairly random number (seconds)... but could prevent waiting forever to get a result
        @curl_setopt(
            $ch, CURLOPT_TIMEOUT, 10
        );   // fairly random number (seconds)... but could prevent waiting forever to get a result
        @curl_setopt(
            $ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1"
        );   // pretend we're a regular browser
        @curl_exec($ch);
        if (@curl_errno($ch)) {   // should be 0
            @curl_close($ch);
            return false;
        }
        $code = @curl_getinfo(
            $ch, CURLINFO_HTTP_CODE
        ); // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);

        return $code;
    }

    /**
     * @param      $url
     * @param bool $followredirects
     *
     * @return array|bool
     */
    public static function getHttpResponseUsingCurl($url, $followredirects = true)
    {
        // returns int responsecode, or false (if url does not exist or connection timeout occurs)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if (!$url || !is_string($url)) {
            return false;
        }
        $ch = @curl_init($url);
        if ($ch === false) {
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        //@curl_setopt($ch, CURLOPT_NOBODY, true);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // catch output (do NOT print!)
        if ($followredirects) {
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt(
                $ch, CURLOPT_MAXREDIRS, 10
            );  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        } else {
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        }
        @curl_setopt(
            $ch, CURLOPT_CONNECTTIMEOUT, 30
        );   // fairly random number (seconds)... but could prevent waiting forever to get a result
        @curl_setopt(
            $ch, CURLOPT_TIMEOUT, 30
        );   // fairly random number (seconds)... but could prevent waiting forever to get a result
        @curl_setopt(
            $ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1"
        );   // pretend we're a regular browser

        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $return = @curl_exec($ch);

        if (@curl_errno($ch)) {   // should be 0
            @curl_close($ch);

            return false;
        }
        $code = @curl_getinfo(
            $ch, CURLINFO_HTTP_CODE
        ); // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);

        return [$code, $return];
    }

    public static function getHttpResponseCodeUsingGetheaders($url, $followredirects = true)
    {
        // returns string responsecode, or false if no responsecode found in headers (or url does not exist)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if (!$url || !is_string($url)) {
            return false;
        }
        $headers = @get_headers($url);
        if ($headers and is_array($headers)) {
            if ($followredirects) {
                // we want the the last errorcode, reverse array so we start at the end:
                $headers = array_reverse($headers);
            }
            foreach ($headers as $hline) {
                // search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
                // note that the exact syntax/version/output differs, so there is some string magic involved here
                if (preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches)) {// "HTTP/*** ### ***"
                    $code = $matches[1];

                    return $code;
                }
            }

            // no HTTP/xxx found in headers:
            return false;
        }

        // no headers :
        return false;
    }

    public static function getMemoryUsed()
    {
        $size = memory_get_usage(true);
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    public static function xmlToArrayWithAttributes($xml, $options = array())
    {
        $defaults       = array(
            'namespaceSeparator' => ':',//you may want this to be something other than a colon
            'attributePrefix'    => '@',   //to distinguish between attributes and nodes with the same name
            'alwaysArray'        => array(),   //array of xml tag names which should always become arrays
            'autoArray'          => true,        //only create arrays for tags which appear more than once
            'textContent'        => '$',       //key used for the text content of elements
            'autoText'           => true,         //skip textContent key if node has no attributes or child nodes
            'keySearch'          => false,       //optional search and replace on tag and attribute names
            'keyReplace'         => false       //replace values for above search values (as passed to str_replace())
        );
        $options        = array_merge($defaults, $options);
        $namespaces     = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) {
                    $attributeName =
                        str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                }
                $attributeKey                   = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = self::xmlToArrayWithAttributes($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch']) {
                    $childTagName =
                        str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                }
                //add namespace prefix, if any
                if ($prefix) {
                    $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
                }

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText        = trim((string)$xml);
        if ($plainText !== '') {
            $textContentArray[$options['textContent']] = $plainText;
        }

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }

    public static function cleanKeywords($value)
    {
        $data = array();
        foreach ((array)$value as $val) {
            if (is_array($val)) {
                $data[] = self::cleanKeywords($val);
            } else {
                $val  = trim($val);
                $val  = str_replace(
                    array(
                        '>',
                        ',',
                        '-',
                        '&'
                    ),
                    '/',
                    $val
                );
                $val  = explode('/', $val);
                $data = array_merge($data, $val);
            }
        }

        foreach ($data as $k => $v) {
            $data[$k] = str_replace('-', ' ', self::urlize($v));
        }

        $data = array_unique(array_filter($data));

        return implode(', ', $data);
    }

    /**
     * @param $amount
     * @param $fromCurrency
     * @param $toCurrency
     *
     * @return float
     */
    public static function currencyConverter($amount, $fromCurrency, $toCurrency)
    {
        if ($amount == 0.00 or $toCurrency == $fromCurrency) {
            return $amount;
        }

        return self::convert($fromCurrency, $toCurrency, $amount);
    }

    /**
     * @param $from
     * @param $toCurrency
     * @param $amount
     *
     * @return float
     */
    private static function convert($from, $toCurrency, $amount)
    {

        $amount     = urlencode($amount);
        $from       = urlencode($from);
        $toCurrency = urlencode($toCurrency);

        $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$toCurrency";

        $handler = curl_init();
        $timeout = 0;
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($handler, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, $timeout);
        $rawData = curl_exec($handler);
        curl_close($handler);
        $data = explode('bld>', $rawData);
        $data = explode($toCurrency, $data[1]);

        return round($data[0], 2);
    }

    /**
     * @param $monthNumber
     *
     * @return int
     */
    public static function getQuarterByMonth($monthNumber)
    {
        return (int)floor(($monthNumber - 1) / 3) + 1;
    }

    public static function cleanLogoName($title)
    {
        return preg_replace(
            '/[^a-z0-9]/iu', '', mb_strtolower(Urlizer::transliterate($title), 'UTF-8')
        );
    }

    /**
     * @param $content
     *
     * @return bool|string
     */
    private static function getHiddenRedirectUrl($content)
    {
        $url = false;
        if (preg_match('/(referLink\.href)\s?\=(.*)\;.*/i', $content, $matches)) {
            $url = trim($matches[2], '"\'');
        } elseif (preg_match('/(window\.location\.replace\()(.*)\).*/i', $content, $moreMatches)) {
            $url = trim($moreMatches[2], '"\'');
        }

        if ($url) {
            if (!$query = parse_url($url, PHP_URL_QUERY)) {
                return $query;
            }
        }

        return $url;
    }

    /**
     * @param Form $form
     * @return string
     */
    public static function renderFormErrors(Form $form)
    {

        if ($errorMessages = $form->getMessages()) {
            $html = '<ol class="form-errors" id="form-error">';

            foreach ($errorMessages as $element => $message) {
                $input = $form->get($element);
                if (is_array($message)) {
                    $field = key($message);
                    $html .= '<li><b>' . $input->getLabel() . '</b> - ' . $message[$field] . '</li>';
                } else {
                    $html .= '<li><b>' . $input->getLabel() . '</b> - ' . $message . '</li>';
                }
            }
            return $html . '</ol>';
        }
        return '';
    }

    /**
     * @param $request
     * @param MvcEvent $event
     * @return array
     */
    public static function getDomainFromRequest($request, MvcEvent $event = null)
    {
        $isConsole = false;
        $host      = null;
        if ($request instanceof ConsoleRequest) {
            $isConsole = true;
            /** @var $routeMatch \Zend\Mvc\Router\RouteMatch */
            if ($event and $routeMatch = $event->getRouteMatch()) {
                $host = $routeMatch->getParam('host', $routeMatch->getParam(self::CLIENT_DOMAIN_KEY, null));
            }
        } elseif ($request instanceof Request and $uri = $request->getUri()) {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $host = $uri->getHost();
        } else {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $host = $request->getServer('HTTP_HOST', $request->getQuery(self::CLIENT_DOMAIN_KEY, null));
        }

        return [$isConsole, self::cleanDomain($host)];
    }

    public static function tidyHtml($html)
    {
        $tidy    = new \Tidy();
        $options = array('indent' => false, 'doctype' => false, 'show-body-only' => true);
        $return  = $tidy->repairString($html, $options, 'UTF8');

        return $return;
    }
}
