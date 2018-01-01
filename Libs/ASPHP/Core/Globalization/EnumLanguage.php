<?php
namespace ASPHP\Core\Globalization;
use \ASPHP\Core\Types\EnumBase;

/**
 * @requires class \ASPHP\Core\Types\EnumBase
 * @version 1.0
 * @author Vigan
 */
class EnumLanguage extends EnumBase
{
    const EN = 'en',
       AL = 'al',
       AR = 'ar',
       BG = 'bg',
       BN_BD = 'bn_BD',
       CA = 'ca',
       CS = 'cs',
       DA = 'da',
       DE = 'de',
       EL = 'el',
       ES = 'es',
       ET = 'et',
       EU = 'eu',
       FA = 'fa',
       FR = 'fr',
       GE = 'ge',
       GL = 'gl',
       HE = 'he',
       HR = 'hr',
       HU = 'hu',
       HY = 'hy',
       ID = 'id',
       IS = 'is',
       IT = 'it',
       JA = 'ja',
       KA = 'ka',
       KK = 'kk',
       KO = 'ko',
       LT = 'lt',
       LV = 'lv',
       MK = 'mk',
       MY = 'my',
       NL = 'nl',
       NO = 'no',
       PL = 'pl',
       PT_BR = 'pt_BR',
       PT_OT = 'pt_PT',
       RO = 'ro',
       RU = 'ru',
       SI = 'si',
       SK = 'sk',
       SL = 'sl',
       SR = 'sr',
       SV = 'sv',
       TH = 'th',
       TJ = 'tj',
       TR = 'tr',
       UK = 'uk',
       VI = 'vi',
       ZH = 'zh';


    /**
     * @param int $member
     * @return string
     */
    public static function GetAsString($member)
    {
        switch($member)
        {
            case 0: static::EN;
            case 1:  static::AL;
            case 2:  static::AR;
            case 3:  static::BG;
            case 4: static::BN_BD;
            case 5:  static::CA;
            case 6:  static::CS;
            case 7:  static::DA;
            case 8:  static::DE;
            case 9:  static::EL;
            case 10: static::ES;
            case 11: static::ET;
            case 12: static::EU;
            case 13: static::FA;
            case 14: static::FR;
            case 15: static::GE;
            case 16: static::GL;
            case 17: static::HE;
            case 18: static::HR;
            case 19: static::HU;
            case 20: static::HY;
            case 21: static::ID;
            case 22: static::IS;
            case 23: static::IT;
            case 24: static::JA;
            case 25: static::KA;
            case 26: static::KK;
            case 27: static::KO;
            case 28: static::LT;
            case 29: static::LV;
            case 30: static::MK;
            case 31: static::MY;
            case 32: static::NL;
            case 33: static::NO;
            case 34: static::PL;
            case 35: static::PT_BR;
            case 36: static::PT_OT;
            case 37: static::RO;
            case 38: static::RU;
            case 39: static::SI;
            case 40: static::SK;
            case 41: static::SL;
            case 42: static::SR;
            case 43: static::SV;
            case 44: static::TH;
            case 45: static::TJ;
            case 46: static::TR;
            case 47: static::UK;
            case 48: static::VI;
            case 49: static::ZH;
            default: return null;
        }
    }

    /**
     * @param string $member
     * @return integer
     */
    public static function GetFromString($member)
    {
        $member = strtoupper($member);
        switch($member)
        {
            case 'EN': return 0;
            case 'AL': return 1;
            case 'AR': return 2;
            case 'BG': return 3;
            case 'BN_BD': return 4;
            case 'CA': return 5;
            case 'CS': return 6;
            case 'DA': return 7;
            case 'DE': return 8;
            case 'EL': return 9;
            case 'ES': return 10;
            case 'ET': return 11;
            case 'EU': return 12;
            case 'FA': return 13;
            case 'FR': return 14;
            case 'GE': return 15;
            case 'GL': return 16;
            case 'HE': return 17;
            case 'HR': return 18;
            case 'HU': return 19;
            case 'HY': return 20;
            case 'ID': return 21;
            case 'IS': return 22;
            case 'IT': return 23;
            case 'JA': return 24;
            case 'KA': return 25;
            case 'KK': return 26;
            case 'KO': return 27;
            case 'LT': return 28;
            case 'LV': return 29;
            case 'MK': return 30;
            case 'MY': return 31;
            case 'NL': return 32;
            case 'NO': return 33;
            case 'PL': return 34;
            case 'PT_BR': return 35;
            case 'PT_OT': return 36;
            case 'RO': return 37;
            case 'RU': return 38;
            case 'SI': return 39;
            case 'SK': return 40;
            case 'SL': return 41;
            case 'SR': return 42;
            case 'SV': return 43;
            case 'TH': return 44;
            case 'TJ': return 45;
            case 'TR': return 46;
            case 'UK': return 47;
            case 'VI': return 48;
            case 'ZH': return 49;
            default: return null;
        }
    }
}
?>