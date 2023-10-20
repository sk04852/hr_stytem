<?php

use App\Http\Controllers\Options\Models\FieldOption;
use App\Http\Controllers\Options\Models\FieldOptionType;
use App\Http\Controllers\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Models\Module;
use Illuminate\Support\Str;

function dateFormat($value)
{
    return date('d M Y', strtotime($value));
}

// Change Password
function changeUserPassword(string $email, string $password, bool $passwordNeedsHash = false)
{
    $user = User::where('email', $email)->first();
    if ($user->setPassword($password, NULL, $passwordNeedsHash)) {
        return true;
    }
    return false;
}

// Register Email, Password
function registerUser(
    string $first_name,
    string $last_name,
    string $email,
    string $password,
    string $type,
    bool $passwordNeedsHash = true,
    int $companyId = NULL
) {
    $user = new User();
    $user->first_name = $first_name;
    $user->last_name = $last_name;
    $user->email = $email;
    $user->type = ucwords($type);
    $user->company_id = $companyId;
    $user->password = ($passwordNeedsHash) ? Hash::make($password) : $password;
    $user->save();
    return $user;
}
// Register Email, Password
function registerModel($data, $modelType)
{
    $user = new User();
    $user->email = $modelType == 'company' ? $data['primary_email'] : $data['email'];
    $user->type = ucwords($modelType);
    $user->status = $data['status'];
    $user->password = Hash::make($data["password"]);
    $user->save();
}

function checkMailExistenceExceptMe($email, $myEmail)
{
    // ignore my email and checking unique in other records
    return User::where('email', '!=', $myEmail)->where('email', '=', $email)->exists();
}

// Updte Email
function updateModel($myEmail, $email)
{
    return User::where('email', '=', $myEmail)->update(['email' => $email]);
}

// Delete Email
function deleteModel($myEmail)
{
    return User::where('email', '=', $myEmail)->delete();
}

function checkMailExistence($email)
{
    return User::where('email', '=', $email)->exists();
}

// Change Password
function changeModelPassword($request, $userId)
{
    $data = $request->all();
    $user = User::find($userId);
    if ($user->setPassword($data["password"])) {
        return true;
    }
    return false;
}

function generateRandomTransactionId($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getModuleIdFromEntity(Model $entity): int
{
    $entityReflection = new \ReflectionClass($entity);
    $nameSpace = $entityReflection->getNamespaceName();
    $splits = explode("\\", $nameSpace);
    array_pop($splits);
    $moduleNameSpace = implode("\\", $splits);
    if (!empty($moduleNameSpace)) {
        $module = Module::where('namespace', $moduleNameSpace)->first();
        return ($module) ? $module->id : 0;
    }
    return 0;
}

function calculateVatAmount(float $amount, float $taxPercentage, bool $taxIncluded = true)
{
    if ($taxPercentage == 0 || $amount == 0)
        return $amount;

    if (!$taxIncluded) {
        $vatAmount  = ($taxPercentage * $amount) / (100 + $taxPercentage);
    } else {
        $vatAmount  = ($amount / 100) * $taxPercentage;
    }
    return $vatAmount;
}

function calculateDiscountAmount(float $amount, string $discount)
{
    $discountedAmount = 0;
    $discountType = "Percentage";
    if (strpos($discount, "%") == -1) {
        if ($discount <= 0) {
            return $amount;
        }
        $discountType = "Amount";
    } else {
        $discount = (float) Str::before($discount, "%");
    }

    if ($discountType === "Percentage") {
        $discountedAmount = ($amount / 100) * $discount;
    } else {
        $discountedAmount = $amount - $discount;
    }
    return $discountedAmount;
}

function getFieldOptionsByType(int $typeId)
{
    return FieldOption::where('type_id', $typeId)->get();
}

function pickFieldOptionFromList($fieldOptionList, $optionName)
{
    return $fieldOptionList->first(function ($value, $key) use ($optionName) {
        return $value->name == $optionName;
    });
}

function mime2ext($mime)
{
    $mime_map = [
        'video/3gpp2'                                                               => '3g2',
        'video/3gp'                                                                 => '3gp',
        'video/3gpp'                                                                => '3gp',
        'application/x-compressed'                                                  => '7zip',
        'audio/x-acc'                                                               => 'aac',
        'audio/ac3'                                                                 => 'ac3',
        'application/postscript'                                                    => 'ai',
        'audio/x-aiff'                                                              => 'aif',
        'audio/aiff'                                                                => 'aif',
        'audio/x-au'                                                                => 'au',
        'video/x-msvideo'                                                           => 'avi',
        'video/msvideo'                                                             => 'avi',
        'video/avi'                                                                 => 'avi',
        'application/x-troff-msvideo'                                               => 'avi',
        'application/macbinary'                                                     => 'bin',
        'application/mac-binary'                                                    => 'bin',
        'application/x-binary'                                                      => 'bin',
        'application/x-macbinary'                                                   => 'bin',
        'image/bmp'                                                                 => 'bmp',
        'image/x-bmp'                                                               => 'bmp',
        'image/x-bitmap'                                                            => 'bmp',
        'image/x-xbitmap'                                                           => 'bmp',
        'image/x-win-bitmap'                                                        => 'bmp',
        'image/x-windows-bmp'                                                       => 'bmp',
        'image/ms-bmp'                                                              => 'bmp',
        'image/x-ms-bmp'                                                            => 'bmp',
        'application/bmp'                                                           => 'bmp',
        'application/x-bmp'                                                         => 'bmp',
        'application/x-win-bitmap'                                                  => 'bmp',
        'application/cdr'                                                           => 'cdr',
        'application/coreldraw'                                                     => 'cdr',
        'application/x-cdr'                                                         => 'cdr',
        'application/x-coreldraw'                                                   => 'cdr',
        'image/cdr'                                                                 => 'cdr',
        'image/x-cdr'                                                               => 'cdr',
        'zz-application/zz-winassoc-cdr'                                            => 'cdr',
        'application/mac-compactpro'                                                => 'cpt',
        'application/pkix-crl'                                                      => 'crl',
        'application/pkcs-crl'                                                      => 'crl',
        'application/x-x509-ca-cert'                                                => 'crt',
        'application/pkix-cert'                                                     => 'crt',
        'text/css'                                                                  => 'css',
        'text/x-comma-separated-values'                                             => 'csv',
        'text/comma-separated-values'                                               => 'csv',
        'application/vnd.msexcel'                                                   => 'csv',
        'application/x-director'                                                    => 'dcr',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
        'application/x-dvi'                                                         => 'dvi',
        'message/rfc822'                                                            => 'eml',
        'application/x-msdownload'                                                  => 'exe',
        'video/x-f4v'                                                               => 'f4v',
        'audio/x-flac'                                                              => 'flac',
        'video/x-flv'                                                               => 'flv',
        'image/gif'                                                                 => 'gif',
        'application/gpg-keys'                                                      => 'gpg',
        'application/x-gtar'                                                        => 'gtar',
        'application/x-gzip'                                                        => 'gzip',
        'application/mac-binhex40'                                                  => 'hqx',
        'application/mac-binhex'                                                    => 'hqx',
        'application/x-binhex40'                                                    => 'hqx',
        'application/x-mac-binhex40'                                                => 'hqx',
        'text/html'                                                                 => 'html',
        'image/x-icon'                                                              => 'ico',
        'image/x-ico'                                                               => 'ico',
        'image/vnd.microsoft.icon'                                                  => 'ico',
        'text/calendar'                                                             => 'ics',
        'application/java-archive'                                                  => 'jar',
        'application/x-java-application'                                            => 'jar',
        'application/x-jar'                                                         => 'jar',
        'image/jp2'                                                                 => 'jp2',
        'video/mj2'                                                                 => 'jp2',
        'image/jpx'                                                                 => 'jp2',
        'image/jpm'                                                                 => 'jp2',
        'image/jpeg'                                                                => 'jpeg',
        'image/pjpeg'                                                               => 'jpeg',
        'application/x-javascript'                                                  => 'js',
        'application/json'                                                          => 'json',
        'text/json'                                                                 => 'json',
        'application/vnd.google-earth.kml+xml'                                      => 'kml',
        'application/vnd.google-earth.kmz'                                          => 'kmz',
        'text/x-log'                                                                => 'log',
        'audio/x-m4a'                                                               => 'm4a',
        'audio/mp4'                                                                 => 'm4a',
        'application/vnd.mpegurl'                                                   => 'm4u',
        'audio/midi'                                                                => 'mid',
        'application/vnd.mif'                                                       => 'mif',
        'video/quicktime'                                                           => 'mov',
        'video/x-sgi-movie'                                                         => 'movie',
        'audio/mpeg'                                                                => 'mp3',
        'audio/mpg'                                                                 => 'mp3',
        'audio/mpeg3'                                                               => 'mp3',
        'audio/mp3'                                                                 => 'mp3',
        'video/mp4'                                                                 => 'mp4',
        'video/mpeg'                                                                => 'mpeg',
        'application/oda'                                                           => 'oda',
        'audio/ogg'                                                                 => 'ogg',
        'video/ogg'                                                                 => 'ogg',
        'application/ogg'                                                           => 'ogg',
        'font/otf'                                                                  => 'otf',
        'application/x-pkcs10'                                                      => 'p10',
        'application/pkcs10'                                                        => 'p10',
        'application/x-pkcs12'                                                      => 'p12',
        'application/x-pkcs7-signature'                                             => 'p7a',
        'application/pkcs7-mime'                                                    => 'p7c',
        'application/x-pkcs7-mime'                                                  => 'p7c',
        'application/x-pkcs7-certreqresp'                                           => 'p7r',
        'application/pkcs7-signature'                                               => 'p7s',
        'application/pdf'                                                           => 'pdf',
        'application/octet-stream'                                                  => 'pdf',
        'application/x-x509-user-cert'                                              => 'pem',
        'application/x-pem-file'                                                    => 'pem',
        'application/pgp'                                                           => 'pgp',
        'application/x-httpd-php'                                                   => 'php',
        'application/php'                                                           => 'php',
        'application/x-php'                                                         => 'php',
        'text/php'                                                                  => 'php',
        'text/x-php'                                                                => 'php',
        'application/x-httpd-php-source'                                            => 'php',
        'image/png'                                                                 => 'png',
        'image/x-png'                                                               => 'png',
        'application/powerpoint'                                                    => 'ppt',
        'application/vnd.ms-powerpoint'                                             => 'ppt',
        'application/vnd.ms-office'                                                 => 'ppt',
        'application/msword'                                                        => 'doc',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/x-photoshop'                                                   => 'psd',
        'image/vnd.adobe.photoshop'                                                 => 'psd',
        'audio/x-realaudio'                                                         => 'ra',
        'audio/x-pn-realaudio'                                                      => 'ram',
        'application/x-rar'                                                         => 'rar',
        'application/rar'                                                           => 'rar',
        'application/x-rar-compressed'                                              => 'rar',
        'audio/x-pn-realaudio-plugin'                                               => 'rpm',
        'application/x-pkcs7'                                                       => 'rsa',
        'text/rtf'                                                                  => 'rtf',
        'text/richtext'                                                             => 'rtx',
        'video/vnd.rn-realvideo'                                                    => 'rv',
        'application/x-stuffit'                                                     => 'sit',
        'application/smil'                                                          => 'smil',
        'text/srt'                                                                  => 'srt',
        'image/svg+xml'                                                             => 'svg',
        'application/x-shockwave-flash'                                             => 'swf',
        'application/x-tar'                                                         => 'tar',
        'application/x-gzip-compressed'                                             => 'tgz',
        'image/tiff'                                                                => 'tiff',
        'font/ttf'                                                                  => 'ttf',
        'text/plain'                                                                => 'txt',
        'text/x-vcard'                                                              => 'vcf',
        'application/videolan'                                                      => 'vlc',
        'text/vtt'                                                                  => 'vtt',
        'audio/x-wav'                                                               => 'wav',
        'audio/wave'                                                                => 'wav',
        'audio/wav'                                                                 => 'wav',
        'application/wbxml'                                                         => 'wbxml',
        'video/webm'                                                                => 'webm',
        'image/webp'                                                                => 'webp',
        'audio/x-ms-wma'                                                            => 'wma',
        'application/wmlc'                                                          => 'wmlc',
        'video/x-ms-wmv'                                                            => 'wmv',
        'video/x-ms-asf'                                                            => 'wmv',
        'font/woff'                                                                 => 'woff',
        'font/woff2'                                                                => 'woff2',
        'application/xhtml+xml'                                                     => 'xhtml',
        'application/excel'                                                         => 'xl',
        'application/msexcel'                                                       => 'xls',
        'application/x-msexcel'                                                     => 'xls',
        'application/x-ms-excel'                                                    => 'xls',
        'application/x-excel'                                                       => 'xls',
        'application/x-dos_ms_excel'                                                => 'xls',
        'application/xls'                                                           => 'xls',
        'application/x-xls'                                                         => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
        'application/vnd.ms-excel'                                                  => 'xlsx',
        'application/xml'                                                           => 'xml',
        'text/xml'                                                                  => 'xml',
        'text/xsl'                                                                  => 'xsl',
        'application/xspf+xml'                                                      => 'xspf',
        'application/x-compress'                                                    => 'z',
        'application/x-zip'                                                         => 'zip',
        'application/zip'                                                           => 'zip',
        'application/x-zip-compressed'                                              => 'zip',
        'application/s-compressed'                                                  => 'zip',
        'multipart/x-zip'                                                           => 'zip',
        'text/x-scriptzsh'                                                          => 'zsh',
    ];

    return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
}

function getAuthType()
{
    $type = request()->header('auth-type');
    if (is_null($type)) {
        $type = request()->get('auth_type', 'user');
        return $type;
    }
    return strtolower($type);
}

function getClient()
{
    global $app;
    return $app['client'];
}

function getUserId()
{
    global $app;
    return $app['user_id'];
}

function getCompanyId()
{
    global $app;
    return $app['company_id'];
}

function vaidateACLProfileAttributtes($attribute, $value, $fail)
{
    $passed = false;
    if (count($value) > 0) {
        $moduleIds = [];
        foreach ($value as $moduleId => $permissions) {
            $moduleIds[] = $moduleId;
        }
        $modules = Module::whereIn('id', $moduleIds)->get();
        if ($modules->count() > 0) {
            $passed = true;
        }
    }
    if (!$passed) {
        return $fail("Attributes are not properly formed");
    }
}

function fieldOptionNameToId(string $fieldOptionName, $typeId = null)
{
    $queryBuilder = FieldOption::where('name', $fieldOptionName);
    if ($typeId) {
        $queryBuilder = $queryBuilder->where('type_id', $typeId);
    }
    $fieldOption = $queryBuilder->first();
    return ($fieldOption != null) ? $fieldOption->id : null;
}


//  Create Keyword Tags from Text With PHP
//  http://www.beliefmedia.com/create-keywords


function beliefmedia_keywords($string, $min_word_length = 3, $min_word_occurrence = 2, $as_array = false, $max_words = 8, $restrict = false)
{

    function keyword_count_sort($first, $sec)
    {

        return $sec[1] - $first[1];
    }

    $string = preg_replace('/[^\p{L}0-9 ]/', ' ', $string);
    $string = trim(preg_replace('/\s+/', ' ', $string));
    $words = explode(' ', $string);

    //  Only compare to common words if $restrict is set to false
    //  Tags are returned based on any word in text
    //  If we don't restrict tag usage, we'll remove common words from array


    if ($restrict === false) {

        $commonWords = array('a', 'able', 'about', 'above', 'abroad', 'according', 'accordingly', 'across', 'actually', 'adj', 'after', 'afterwards', 'again', 'against', 'ago', 'ahead', 'ain\'t', 'all', 'allow', 'allows', 'almost', 'alone', 'along', 'alongside', 'already', 'also', 'although', 'always', 'am', 'amid', 'amidst', 'among', 'amongst', 'an', 'and', 'another', 'any', 'anybody', 'anyhow', 'anyone', 'anything', 'anyway', 'anyways', 'anywhere', 'apart', 'appear', 'appreciate', 'appropriate', 'are', 'aren\'t', 'around', 'as', 'a\'s', 'aside', 'ask', 'asking', 'associated', 'at', 'available', 'away', 'awfully', 'b', 'back', 'backward', 'backwards', 'be', 'became', 'because', 'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'begin', 'behind', 'being', 'believe', 'below', 'beside', 'besides', 'best', 'better', 'between', 'beyond', 'both', 'brief', 'but', 'by', 'c', 'came', 'can', 'cannot', 'cant', 'can\'t', 'caption', 'cause', 'causes', 'certain', 'certainly', 'changes', 'clearly', 'c\'mon', 'co', 'co.', 'com', 'come', 'comes', 'concerning', 'consequently', 'consider', 'considering', 'contain', 'containing', 'contains', 'corresponding', 'could', 'couldn\'t', 'course', 'c\'s', 'currently', 'd', 'dare', 'daren\'t', 'definitely', 'described', 'despite', 'did', 'didn\'t', 'different', 'directly', 'do', 'does', 'doesn\'t', 'doing', 'done', 'don\'t', 'down', 'downwards', 'during', 'e', 'each', 'edu', 'eg', 'eight', 'eighty', 'either', 'else', 'elsewhere', 'end', 'ending', 'enough', 'entirely', 'especially', 'et', 'etc', 'even', 'ever', 'evermore', 'every', 'everybody', 'everyone', 'everything', 'everywhere', 'ex', 'exactly', 'example', 'except', 'f', 'fairly', 'far', 'farther', 'few', 'fewer', 'fifth', 'first', 'five', 'followed', 'following', 'follows', 'for', 'forever', 'former', 'formerly', 'forth', 'forward', 'found', 'four', 'from', 'further', 'furthermore', 'g', 'get', 'gets', 'getting', 'given', 'gives', 'go', 'goes', 'going', 'gone', 'got', 'gotten', 'greetings', 'h', 'had', 'hadn\'t', 'half', 'happens', 'hardly', 'has', 'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'d', 'he\'ll', 'hello', 'help', 'hence', 'her', 'here', 'hereafter', 'hereby', 'herein', 'here\'s', 'hereupon', 'hers', 'herself', 'he\'s', 'hi', 'him', 'himself', 'his', 'hither', 'home', 'hopefully', 'how', 'howbeit', 'however', 'hundred', 'i', 'i\'d', 'ie', 'if', 'ignored', 'i\'ll', 'i\'m', 'immediate', 'in', 'inasmuch', 'inc', 'inc.', 'indeed', 'indicate', 'indicated', 'indicates', 'inner', 'inside', 'insofar', 'instead', 'into', 'inward', 'is', 'isn\'t', 'it', 'it\'d', 'it\'ll', 'its', 'it\'s', 'itself', 'i\'ve', 'j', 'just', 'k', 'keep', 'keeps', 'kept', 'know', 'known', 'knows', 'l', 'last', 'lately', 'later', 'latter', 'latterly', 'least', 'less', 'lest', 'let', 'let\'s', 'like', 'liked', 'likely', 'likewise', 'little', 'look', 'looking', 'looks', 'low', 'lower', 'ltd', 'm', 'made', 'mainly', 'make', 'makes', 'many', 'may', 'maybe', 'mayn\'t', 'me', 'mean', 'meantime', 'meanwhile', 'merely', 'might', 'mightn\'t', 'mine', 'minus', 'miss', 'more', 'moreover', 'most', 'mostly', 'mr', 'mrs', 'much', 'must', 'mustn\'t', 'my', 'myself', 'n', 'name', 'namely', 'nd', 'near', 'nearly', 'necessary', 'need', 'needn\'t', 'needs', 'neither', 'never', 'neverf', 'neverless', 'nevertheless', 'new', 'next', 'nine', 'ninety', 'no', 'nobody', 'non', 'none', 'nonetheless', 'noone', 'no-one', 'nor', 'normally', 'not', 'nothing', 'notwithstanding', 'novel', 'now', 'nowhere', 'o', 'obviously', 'of', 'off', 'often', 'oh', 'ok', 'okay', 'old', 'on', 'once', 'one', 'ones', 'one\'s', 'only', 'onto', 'opposite', 'or', 'other', 'others', 'otherwise', 'ought', 'oughtn\'t', 'our', 'ours', 'ourselves', 'out', 'outside', 'over', 'overall', 'own', 'p', 'particular', 'particularly', 'past', 'per', 'perhaps', 'placed', 'please', 'plus', 'possible', 'presumably', 'probably', 'provided', 'provides', 'q', 'que', 'quite', 'qv', 'r', 'rather', 'rd', 're', 'really', 'reasonably', 'recent', 'recently', 'regarding', 'regardless', 'regards', 'relatively', 'respectively', 'right', 'round', 's', 'said', 'same', 'saw', 'say', 'saying', 'says', 'second', 'secondly', 'see', 'seeing', 'seem', 'seemed', 'seeming', 'seems', 'seen', 'self', 'selves', 'sensible', 'sent', 'serious', 'seriously', 'seven', 'several', 'shall', 'shan\'t', 'she', 'she\'d', 'she\'ll', 'she\'s', 'should', 'shouldn\'t', 'since', 'six', 'so', 'some', 'somebody', 'someday', 'somehow', 'someone', 'something', 'sometime', 'sometimes', 'somewhat', 'somewhere', 'soon', 'sorry', 'specified', 'specify', 'specifying', 'still', 'sub', 'such', 'sup', 'sure', 't', 'take', 'taken', 'taking', 'tell', 'tends', 'th', 'than', 'thank', 'thanks', 'thanx', 'that', 'that\'ll', 'thats', 'that\'s', 'that\'ve', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'thence', 'there', 'thereafter', 'thereby', 'there\'d', 'therefore', 'therein', 'there\'ll', 'there\'re', 'theres', 'there\'s', 'thereupon', 'there\'ve', 'these', 'they', 'they\'d', 'they\'ll', 'they\'re', 'they\'ve', 'thing', 'things', 'think', 'third', 'thirty', 'this', 'thorough', 'thoroughly', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'till', 'to', 'together', 'too', 'took', 'toward', 'towards', 'tried', 'tries', 'truly', 'try', 'trying', 't\'s', 'twice', 'two', 'u', 'un', 'under', 'underneath', 'undoing', 'unfortunately', 'unless', 'unlike', 'unlikely', 'until', 'unto', 'up', 'upon', 'upwards', 'us', 'use', 'used', 'useful', 'uses', 'using', 'usually', 'v', 'value', 'various', 'versus', 'very', 'via', 'viz', 'vs', 'w', 'want', 'wants', 'was', 'wasn\'t', 'way', 'we', 'we\'d', 'welcome', 'well', 'we\'ll', 'went', 'were', 'we\'re', 'weren\'t', 'we\'ve', 'what', 'whatever', 'what\'ll', 'what\'s', 'what\'ve', 'when', 'whence', 'whenever', 'where', 'whereafter', 'whereas', 'whereby', 'wherein', 'where\'s', 'whereupon', 'wherever', 'whether', 'which', 'whichever', 'while', 'whilst', 'whither', 'who', 'who\'d', 'whoever', 'whole', 'who\'ll', 'whom', 'whomever', 'who\'s', 'whose', 'why', 'will', 'willing', 'wish', 'with', 'within', 'without', 'wonder', 'won\'t', 'would', 'wouldn\'t', 'x', 'y', 'yes', 'yet', 'you', 'you\'d', 'you\'ll', 'your', 'you\'re', 'yours', 'yourself', 'yourselves', 'you\'ve', 'z', 'zero');
        
        $words = array_udiff($words, $commonWords, 'strcasecmp');
    }

    /* Restrict Keywords based on values in the $allowedWords array */

    if ($restrict !== false) {

        $allowedWords =  array('mees, naine, inglise, vene, soome, eesti');

        $words = array_uintersect($words, $allowedWords, 'strcasecmp');
    }

    $keywords = array();

    while (($c_word = array_shift($words)) !== null) {

        if (strlen($c_word) < $min_word_length) continue;

        $c_word = strtolower($c_word);

        if (array_key_exists($c_word, $keywords)) $keywords[$c_word][1]++;

        else $keywords[$c_word] = array($c_word, 1);
    }

    usort($keywords, 'keyword_count_sort');
    $final_keywords = array();


    foreach ($keywords as $keyword_det) {

        if ($keyword_det[1] < $min_word_occurrence) break;

        array_push($final_keywords, $keyword_det[0]);
    }

    $final_keywords = array_slice($final_keywords, 0, $max_words);

    return $as_array ? $final_keywords : implode(', ', $final_keywords);
}

function extract_keywords ($text){
    $keywords = [];
    $extract_keywords_list = config('extract_keywords');
    $string = trim(preg_replace('/\s+/', ' ', $text));
    $string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
    $words = explode(' ', $string);
    while (($c_word = array_shift($words)) !== null) {

        $c_word = strtolower($c_word);

        if(in_array($c_word, $extract_keywords_list)){
            if (array_key_exists($c_word, $keywords)) $keywords[$c_word][1]++;

            else $keywords[$c_word] = array($c_word, 1);
        }
        
    }

    return $keywords;
}
