<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Core\Field;

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbValidator;

\defined( 'CBLIB' ) or die();

class PredefinedField extends TextField
{
	/**
	 * formats variable array into data attribute string
	 *
	 * @param  FieldTable   $field
	 * @param  UserTable    $user
	 * @param  string       $output
	 * @param  string       $reason
	 * @param  array        $attributeArray
	 * @return null|string
	 */
	protected function getDataAttributes( $field, $user, $output, $reason, $attributeArray = array() )
	{
		global $_CB_framework, $ueConfig;

		if ( $field->name == 'username' ) {
			$attributeArray[]		=	cbValidator::getRuleHtmlAttributes( 'cbusername' );

			if ( $ueConfig['reg_username_checker'] == 1 ) {
				$attributeArray[]	=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => (int) $user->id, 'field' => htmlspecialchars( $field->name ), 'reason' => htmlspecialchars( $reason ) ) );
			}
		} elseif ( $field->name == 'alias' ) {
			$aliasReg				=	'/^[a-zA-Z][a-zA-Z0-9\-]+$/';

			if ( $_CB_framework->getCfg( 'unicodeslugs' ) == 1 ) {
				// ES2015 Unicode regular expression in ES5 of ES6 /^[a-zA-Z\p{L}][a-zA-Z0-9\-\p{L}]+$/u using RegexpU https://github.com/mathiasbynens/regexpu
				// online at https://mothereff.in/regexpu
				$aliasReg			=	'/^(?:[A-Za-z\xAA\xB5\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u037F\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u052F\u0531-\u0556\u0559\u0560-\u0588\u05D0-\u05EA\u05EF-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u0860-\u086A\u08A0-\u08B4\u08B6-\u08BD\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0980\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u09FC\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0AF9\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D\u0C58-\u0C5A\u0C60\u0C61\u0C80\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D54-\u0D56\u0D5F-\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F5\u13F8-\u13FD\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16F1-\u16F8\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1878\u1880-\u1884\u1887-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191E\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19B0-\u19C9\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1C80-\u1C88\u1C90-\u1CBA\u1CBD-\u1CBF\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312F\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FEF\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA69D\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA7B9\uA7F7-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA8FD\uA8FE\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uA9E0-\uA9E4\uA9E6-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA7E-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uAB30-\uAB5A\uAB5C-\uAB65\uAB70-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]|\uD800[\uDC00-\uDC0B\uDC0D-\uDC26\uDC28-\uDC3A\uDC3C\uDC3D\uDC3F-\uDC4D\uDC50-\uDC5D\uDC80-\uDCFA\uDE80-\uDE9C\uDEA0-\uDED0\uDF00-\uDF1F\uDF2D-\uDF40\uDF42-\uDF49\uDF50-\uDF75\uDF80-\uDF9D\uDFA0-\uDFC3\uDFC8-\uDFCF]|\uD801[\uDC00-\uDC9D\uDCB0-\uDCD3\uDCD8-\uDCFB\uDD00-\uDD27\uDD30-\uDD63\uDE00-\uDF36\uDF40-\uDF55\uDF60-\uDF67]|\uD802[\uDC00-\uDC05\uDC08\uDC0A-\uDC35\uDC37\uDC38\uDC3C\uDC3F-\uDC55\uDC60-\uDC76\uDC80-\uDC9E\uDCE0-\uDCF2\uDCF4\uDCF5\uDD00-\uDD15\uDD20-\uDD39\uDD80-\uDDB7\uDDBE\uDDBF\uDE00\uDE10-\uDE13\uDE15-\uDE17\uDE19-\uDE35\uDE60-\uDE7C\uDE80-\uDE9C\uDEC0-\uDEC7\uDEC9-\uDEE4\uDF00-\uDF35\uDF40-\uDF55\uDF60-\uDF72\uDF80-\uDF91]|\uD803[\uDC00-\uDC48\uDC80-\uDCB2\uDCC0-\uDCF2\uDD00-\uDD23\uDF00-\uDF1C\uDF27\uDF30-\uDF45]|\uD804[\uDC03-\uDC37\uDC83-\uDCAF\uDCD0-\uDCE8\uDD03-\uDD26\uDD44\uDD50-\uDD72\uDD76\uDD83-\uDDB2\uDDC1-\uDDC4\uDDDA\uDDDC\uDE00-\uDE11\uDE13-\uDE2B\uDE80-\uDE86\uDE88\uDE8A-\uDE8D\uDE8F-\uDE9D\uDE9F-\uDEA8\uDEB0-\uDEDE\uDF05-\uDF0C\uDF0F\uDF10\uDF13-\uDF28\uDF2A-\uDF30\uDF32\uDF33\uDF35-\uDF39\uDF3D\uDF50\uDF5D-\uDF61]|\uD805[\uDC00-\uDC34\uDC47-\uDC4A\uDC80-\uDCAF\uDCC4\uDCC5\uDCC7\uDD80-\uDDAE\uDDD8-\uDDDB\uDE00-\uDE2F\uDE44\uDE80-\uDEAA\uDF00-\uDF1A]|\uD806[\uDC00-\uDC2B\uDCA0-\uDCDF\uDCFF\uDE00\uDE0B-\uDE32\uDE3A\uDE50\uDE5C-\uDE83\uDE86-\uDE89\uDE9D\uDEC0-\uDEF8]|\uD807[\uDC00-\uDC08\uDC0A-\uDC2E\uDC40\uDC72-\uDC8F\uDD00-\uDD06\uDD08\uDD09\uDD0B-\uDD30\uDD46\uDD60-\uDD65\uDD67\uDD68\uDD6A-\uDD89\uDD98\uDEE0-\uDEF2]|\uD808[\uDC00-\uDF99]|\uD809[\uDC80-\uDD43]|[\uD80C\uD81C-\uD820\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872\uD874-\uD879][\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2E]|\uD811[\uDC00-\uDE46]|\uD81A[\uDC00-\uDE38\uDE40-\uDE5E\uDED0-\uDEED\uDF00-\uDF2F\uDF40-\uDF43\uDF63-\uDF77\uDF7D-\uDF8F]|\uD81B[\uDE40-\uDE7F\uDF00-\uDF44\uDF50\uDF93-\uDF9F\uDFE0\uDFE1]|\uD821[\uDC00-\uDFF1]|\uD822[\uDC00-\uDEF2]|\uD82C[\uDC00-\uDD1E\uDD70-\uDEFB]|\uD82F[\uDC00-\uDC6A\uDC70-\uDC7C\uDC80-\uDC88\uDC90-\uDC99]|\uD835[\uDC00-\uDC54\uDC56-\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD1E-\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD52-\uDEA5\uDEA8-\uDEC0\uDEC2-\uDEDA\uDEDC-\uDEFA\uDEFC-\uDF14\uDF16-\uDF34\uDF36-\uDF4E\uDF50-\uDF6E\uDF70-\uDF88\uDF8A-\uDFA8\uDFAA-\uDFC2\uDFC4-\uDFCB]|\uD83A[\uDC00-\uDCC4\uDD00-\uDD43]|\uD83B[\uDE00-\uDE03\uDE05-\uDE1F\uDE21\uDE22\uDE24\uDE27\uDE29-\uDE32\uDE34-\uDE37\uDE39\uDE3B\uDE42\uDE47\uDE49\uDE4B\uDE4D-\uDE4F\uDE51\uDE52\uDE54\uDE57\uDE59\uDE5B\uDE5D\uDE5F\uDE61\uDE62\uDE64\uDE67-\uDE6A\uDE6C-\uDE72\uDE74-\uDE77\uDE79-\uDE7C\uDE7E\uDE80-\uDE89\uDE8B-\uDE9B\uDEA1-\uDEA3\uDEA5-\uDEA9\uDEAB-\uDEBB]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1\uDEB0-\uDFFF]|\uD87A[\uDC00-\uDFE0]|\uD87E[\uDC00-\uDE1D])(?:[\x2D0-9A-Za-z\xAA\xB5\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u037F\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u052F\u0531-\u0556\u0559\u0560-\u0588\u05D0-\u05EA\u05EF-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u0860-\u086A\u08A0-\u08B4\u08B6-\u08BD\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0980\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u09FC\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0AF9\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D\u0C58-\u0C5A\u0C60\u0C61\u0C80\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D54-\u0D56\u0D5F-\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F5\u13F8-\u13FD\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16F1-\u16F8\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1878\u1880-\u1884\u1887-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191E\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19B0-\u19C9\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1C80-\u1C88\u1C90-\u1CBA\u1CBD-\u1CBF\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312F\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FEF\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA69D\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA7B9\uA7F7-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA8FD\uA8FE\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uA9E0-\uA9E4\uA9E6-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA7E-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uAB30-\uAB5A\uAB5C-\uAB65\uAB70-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]|\uD800[\uDC00-\uDC0B\uDC0D-\uDC26\uDC28-\uDC3A\uDC3C\uDC3D\uDC3F-\uDC4D\uDC50-\uDC5D\uDC80-\uDCFA\uDE80-\uDE9C\uDEA0-\uDED0\uDF00-\uDF1F\uDF2D-\uDF40\uDF42-\uDF49\uDF50-\uDF75\uDF80-\uDF9D\uDFA0-\uDFC3\uDFC8-\uDFCF]|\uD801[\uDC00-\uDC9D\uDCB0-\uDCD3\uDCD8-\uDCFB\uDD00-\uDD27\uDD30-\uDD63\uDE00-\uDF36\uDF40-\uDF55\uDF60-\uDF67]|\uD802[\uDC00-\uDC05\uDC08\uDC0A-\uDC35\uDC37\uDC38\uDC3C\uDC3F-\uDC55\uDC60-\uDC76\uDC80-\uDC9E\uDCE0-\uDCF2\uDCF4\uDCF5\uDD00-\uDD15\uDD20-\uDD39\uDD80-\uDDB7\uDDBE\uDDBF\uDE00\uDE10-\uDE13\uDE15-\uDE17\uDE19-\uDE35\uDE60-\uDE7C\uDE80-\uDE9C\uDEC0-\uDEC7\uDEC9-\uDEE4\uDF00-\uDF35\uDF40-\uDF55\uDF60-\uDF72\uDF80-\uDF91]|\uD803[\uDC00-\uDC48\uDC80-\uDCB2\uDCC0-\uDCF2\uDD00-\uDD23\uDF00-\uDF1C\uDF27\uDF30-\uDF45]|\uD804[\uDC03-\uDC37\uDC83-\uDCAF\uDCD0-\uDCE8\uDD03-\uDD26\uDD44\uDD50-\uDD72\uDD76\uDD83-\uDDB2\uDDC1-\uDDC4\uDDDA\uDDDC\uDE00-\uDE11\uDE13-\uDE2B\uDE80-\uDE86\uDE88\uDE8A-\uDE8D\uDE8F-\uDE9D\uDE9F-\uDEA8\uDEB0-\uDEDE\uDF05-\uDF0C\uDF0F\uDF10\uDF13-\uDF28\uDF2A-\uDF30\uDF32\uDF33\uDF35-\uDF39\uDF3D\uDF50\uDF5D-\uDF61]|\uD805[\uDC00-\uDC34\uDC47-\uDC4A\uDC80-\uDCAF\uDCC4\uDCC5\uDCC7\uDD80-\uDDAE\uDDD8-\uDDDB\uDE00-\uDE2F\uDE44\uDE80-\uDEAA\uDF00-\uDF1A]|\uD806[\uDC00-\uDC2B\uDCA0-\uDCDF\uDCFF\uDE00\uDE0B-\uDE32\uDE3A\uDE50\uDE5C-\uDE83\uDE86-\uDE89\uDE9D\uDEC0-\uDEF8]|\uD807[\uDC00-\uDC08\uDC0A-\uDC2E\uDC40\uDC72-\uDC8F\uDD00-\uDD06\uDD08\uDD09\uDD0B-\uDD30\uDD46\uDD60-\uDD65\uDD67\uDD68\uDD6A-\uDD89\uDD98\uDEE0-\uDEF2]|\uD808[\uDC00-\uDF99]|\uD809[\uDC80-\uDD43]|[\uD80C\uD81C-\uD820\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872\uD874-\uD879][\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2E]|\uD811[\uDC00-\uDE46]|\uD81A[\uDC00-\uDE38\uDE40-\uDE5E\uDED0-\uDEED\uDF00-\uDF2F\uDF40-\uDF43\uDF63-\uDF77\uDF7D-\uDF8F]|\uD81B[\uDE40-\uDE7F\uDF00-\uDF44\uDF50\uDF93-\uDF9F\uDFE0\uDFE1]|\uD821[\uDC00-\uDFF1]|\uD822[\uDC00-\uDEF2]|\uD82C[\uDC00-\uDD1E\uDD70-\uDEFB]|\uD82F[\uDC00-\uDC6A\uDC70-\uDC7C\uDC80-\uDC88\uDC90-\uDC99]|\uD835[\uDC00-\uDC54\uDC56-\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD1E-\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD52-\uDEA5\uDEA8-\uDEC0\uDEC2-\uDEDA\uDEDC-\uDEFA\uDEFC-\uDF14\uDF16-\uDF34\uDF36-\uDF4E\uDF50-\uDF6E\uDF70-\uDF88\uDF8A-\uDFA8\uDFAA-\uDFC2\uDFC4-\uDFCB]|\uD83A[\uDC00-\uDCC4\uDD00-\uDD43]|\uD83B[\uDE00-\uDE03\uDE05-\uDE1F\uDE21\uDE22\uDE24\uDE27\uDE29-\uDE32\uDE34-\uDE37\uDE39\uDE3B\uDE42\uDE47\uDE49\uDE4B\uDE4D-\uDE4F\uDE51\uDE52\uDE54\uDE57\uDE59\uDE5B\uDE5D\uDE5F\uDE61\uDE62\uDE64\uDE67-\uDE6A\uDE6C-\uDE72\uDE74-\uDE77\uDE79-\uDE7C\uDE7E\uDE80-\uDE89\uDE8B-\uDE9B\uDEA1-\uDEA3\uDEA5-\uDEA9\uDEAB-\uDEBB]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1\uDEB0-\uDFFF]|\uD87A[\uDC00-\uDFE0]|\uD87E[\uDC00-\uDE1D])+$/';
			}

			$attributeArray[]		=	cbValidator::getRuleHtmlAttributes( 'pattern', $aliasReg, CBTxt::T( 'VALIDATION_ERROR_FIELD_ALIAS', 'Please enter a valid alphanumeric profile url. Must begin with a letter and contain letters, numbers, and dashes only.' ) );
			$attributeArray[]		=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => (int) $user->id, 'field' => htmlspecialchars( $field->name ), 'reason' => htmlspecialchars( $reason ) ) );
		}

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
	}

	/**
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		global $_CB_framework, $ueConfig;

		$value									=	$user->get( $field->get( 'name' ) );

		switch ( $output ) {
			case 'html':
			case 'rss':
				$profileLink					=	$user->get( '_allowProfileLink', $field->get( '_allowProfileLink', null, GetterInterface::BOOLEAN ), GetterInterface::BOOLEAN ); // For B/C

				if ( $profileLink === null ) {
					$profileLink				=	$field->params->get( 'fieldProfileLink', true, GetterInterface::BOOLEAN );
				}

				if ( $field->name == 'alias' ) {
					$url						=	$_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $user->get( 'id', 0, GetterInterface::INT ) ) );

					return $this->formatFieldValueLayout( '<a href="' . $url . '">' . $url . '</a>', $reason, $field, $user );
				} elseif ( ( $field->type == 'predefined' ) && $profileLink && ( $reason != 'profile' ) && ( $reason != 'edit' ) ) {
					return $this->formatFieldValueLayout( '<a href="' . $_CB_framework->userProfileUrl( $user->id, true ) . '">' . htmlspecialchars( $value ) . '</a>', $reason, $field, $user );
				} else {
					return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				}
				break;
			case 'htmledit':
				if ( $field->name == 'username' ) {
					if ( ! ( ( $ueConfig['usernameedit'] == 0 ) && ( $reason == 'edit' ) && ( $_CB_framework->getUi() == 1 ) ) ) {
						$profile				=	$field->get( 'profile' );

						$field->set( 'profile', 1 );

						$return					=	parent::getField( $field, $user, $output, $reason, $list_compare_types );

						$field->set( 'profile', $profile );
					} else {
						$field->set( 'readonly', 1 );

						$return					=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
					}
				} elseif ( $field->name == 'alias' ) {
					if ( ! $_CB_framework->getCfg( 'sef' ) ) {
						// SEF isn't enabled so just do nothing:
						return null;
					}

					// Alias is always visible so force its icon to show visible on profile:
					$profile					=	$field->get( 'profile' );

					$field->set( 'profile', 1 );

					$return						=	parent::getField( $field, $user, $output, $reason, $list_compare_types );

					if ( ! Application::Cms()->getClientId() ) {
						$alias					=	$value;

						if ( ! $alias ) {
							$alias				=	$user->get( 'username', null, GetterInterface::STRING );
						}

						$aliasReg				=	'/(^[^a-zA-Z])|[^a-zA-Z0-9\-]/';

						if ( $_CB_framework->getCfg( 'unicodeslugs' ) == 1 ) {
							$aliasReg			=	'/(^[^a-zA-Z\p{L}])|[^a-zA-Z0-9\-\p{L}]/u';
						}

						if ( is_numeric( $alias ) || preg_match( $aliasReg, $alias ) || ( $alias != Application::Router()->stringToAlias( $alias ) ) || in_array( $alias, Application::Router()->getViews() ) ) {
							$alias				=	$user->get( 'id', 0, GetterInterface::INT ) . '-' . Application::Router()->stringToAlias( $alias );
						}

						$return					.=	'<div class="small text-muted mt-1 cbCurrentProfileURL">' . CBTxt::Th( 'YOUR_CURRENT_PROFILE_URL_IS_URL', 'Your current Profile URL is: [url]', array( '[url]' => $_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $user->get( 'id', 0, GetterInterface::INT ) ) ), '[alias]' => $alias ) ) . '</div>';
					}

					$field->set( 'profile', $profile );
				} else {
					$return						=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				}

				return $return;
				break;
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check
	 * that the logged-in user has rights to edit that $user.
	 *
	 * @param FieldTable     $field
	 * @param null|UserTable $user
	 * @param array          $postdata
	 * @param string         $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches (always public!)
	 * @return string                  Expected output.
	 */
	public function fieldClass( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_database, $ueConfig, $_GET;

		parent::fieldClass( $field, $user, $postdata, $reason ); // Performs spoof check

		if ( ! in_array( $reason, array( 'edit', 'register' ) ) ) {
			return null; // wrong reason; do nothing
		}

		$function						=	cbGetParam( $_GET, 'function', '' );

		if ( ! in_array( $function, array( 'checkvalue', 'testexists' ) ) ) {
			return null; // wrong funcion; do nothing
		}

		$fieldName						=	$field->name;
		$ajaxChecker					=	( $fieldName == 'alias' ? 1 : ( isset( $ueConfig['reg_username_checker'] ) ? $ueConfig['reg_username_checker'] : 0 ) );

		if ( ! $ajaxChecker ) {
			return null; // ajax checking is disabled; do nothing
		}

		$valid							=	true;
		$message						=	null;
		$value							=	stripslashes( cbGetParam( $postdata, 'value', '' ) );

		if ( $fieldName == 'alias' ) {
			$value						=	Application::Router()->stringToAlias( $value );
		}

		if ( $value != '' ) {
			if ( ( ! $user ) || ( cbutf8_strtolower( trim( $value ) ) != cbutf8_strtolower( trim( $user->$fieldName ) ) ) ) {
				if ( $function == 'testexists' ) {
					// We're just checking that the exact field value exists or not so don't include the cross-checks (e.g. used on forgot login):
					if ( $fieldName == 'alias' ) {
						$exists			=	$this->checkAliasExists( $value );
					} else {
						$exists			=	$this->checkUsernameExists( $value );
					}
				} else {
					$exists				=	$this->checkUsernameExists( $value );

					if ( ! $exists ) {
						$exists			=	$this->checkAliasExists( $value );
					}
				}

				if ( ( $fieldName == 'alias' ) && in_array( trim( $value ), Application::Router()->getViews() ) ) {
					$exists				=	true;
				}

				if ( $function == 'testexists' ) {
					if ( $exists ) {
						if ( $fieldName == 'alias' ) {
							$message	=	CBTxt::Th( 'VALIDATION_ERROR_FIELD_ALIAS_EXISTS', "The profile url '[alias]' exists on this site.", array( '[alias]' => htmlspecialchars( $value ) ) );
						} else {
							$message	=	CBTxt::Th( 'UE_USERNAME_EXISTS_ON_SITE', "The username '[username]' exists on this site.", array( '[username]' =>  htmlspecialchars( $value ) ) );
						}
					} else {
						$valid			=	false;

						if ( $fieldName == 'alias' ) {
							$message	=	CBTxt::Th( 'VALIDATION_ERROR_FIELD_ALIAS_DOESNT_EXIST', "The profile url '[alias]' does not exist on this site.", array( '[alias]' => htmlspecialchars( $value ) ) );
						} else {
							$message	=	CBTxt::Th( 'UE_USERNAME_DOESNT_EXISTS', "The username '[username]' does not exist on this site.", array( '[username]' =>  htmlspecialchars( $value ) ) );
						}
					}
				} else {
					if ( $exists ) {
						$valid			=	false;

						if ( $fieldName == 'alias' ) {
							$message	=	CBTxt::Th( 'VALIDATION_ERROR_FIELD_ALIAS_NOT_AVAILABLE', "The profile url '[alias]' is already in use.", array( '[alias]' => htmlspecialchars( $value ) ) );
						} else {
							$message	=	CBTxt::Th( 'UE_USERNAME_NOT_AVAILABLE', "The username '[username]' is already in use.", array( '[username]' =>  htmlspecialchars( $value ) ) );
						}
					} else {
						if ( $fieldName == 'alias' ) {
							$message	=	CBTxt::Th( 'VALIDATION_ERROR_FIELD_ALIAS_AVAILABLE', "The profile url '[alias]' is available.", array( '[alias]' => htmlspecialchars( $value ) ) );
						} else {
							$message	=	CBTxt::Th( 'UE_USERNAME_AVAILABLE', "The username '[username]' is available.", array( '[username]' =>  htmlspecialchars( $value ) ) );
						}
					}
				}
			}
		}

		return json_encode( array( 'valid' => $valid, 'message' => $message ) );
	}

	/**
	 * Checks if the username exists or not
	 *
	 * @param $username
	 * @return int
	 */
	private function checkUsernameExists( $username )
	{
		global $_CB_database;

		static $cache			=	array();

		if ( ! isset( $cache[$username] ) ) {
			$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__users' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'username' ) . " = " . $_CB_database->Quote( trim( $username ) );
			$_CB_database->setQuery( $query, 0, 1 );
			$cache[$username]	=	(int) $_CB_database->loadResult();
		}

		return $cache[$username];
	}

	/**
	 * Checks if the alias exists or not
	 *
	 * @param $alias
	 * @return int
	 */
	private function checkAliasExists( $alias )
	{
		global $_CB_database;

		static $cache			=	array();

		if ( ! isset( $cache[$alias] ) ) {
			$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'alias' ) . " = " . $_CB_database->Quote( trim( $alias ) );
			$_CB_database->setQuery( $query, 0, 1 );
			$cache[$alias]		=	(int) $_CB_database->loadResult();
		}

		return $cache[$alias];
	}

	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_framework, $ueConfig;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		switch ( $field->name ) {
			case 'username':
				if ( ! ( ( $ueConfig['usernameedit'] == 0 ) && ( $reason == 'edit' ) && ( $_CB_framework->getUi() == 1 ) ) ) {
					$username				=	stripslashes( cbGetParam( $postdata, 'username', null ) );

					if ( $this->validate( $field, $user, $field->name, $username, $postdata, $reason ) ) {
						if ( ( $username !== null ) && ( $username !== $user->username ) ) {
							$this->_logFieldUpdate( $field, $user, $reason, $user->username, $username );
						}
					}

					if ( $username !== null ) {
						$user->username		=	$username;
					}
				}
				break;
			case 'alias':
				if ( ! $_CB_framework->getCfg( 'sef' ) ) {
					// SEF isn't enabled so just do nothing:
					return;
				}

				$alias						=	Application::Router()->stringToAlias( stripslashes( cbGetParam( $postdata, 'alias', null ) ) );

				if ( $this->validate( $field, $user, $field->name, $alias, $postdata, $reason ) ) {
					if ( ( $alias !== null ) && ( $alias !== $user->alias ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->alias, $alias );
					}
				}

				if ( $alias !== null ) {
					$user->alias			=	$alias;
				}
				break;
			case 'name':
			case 'firstname':
			case 'middlename':
			case 'lastname':
				$value							=	stripslashes( cbGetParam( $postdata, $field->name ) );
				$col							=	$field->name;
				if ( $this->validate( $field, $user, $field->name, $value, $postdata, $reason ) ) {
					if ( ( (string) $user->$col ) !== (string) $value ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
					}
				}
				if ( $value !== null ) {
					// Form name from first/middle/last name if needed:
					if ( $field->name !== 'name' ) {
						$nameArr				=	array();
						if ( $ueConfig['name_style'] >= 2 ) {
							$firstname		=	stripslashes( cbGetParam( $postdata, 'firstname', $user->firstname ) );
							if ( $firstname ) {
								$nameArr[]	=	 $firstname;
							}
							if ( $ueConfig['name_style'] == 3 ) {
								$middlename	=	stripslashes( cbGetParam( $postdata, 'middlename', $user->middlename ) );
								if ( $middlename ) {
									$nameArr[]	=	$middlename;
								}
							}
							$lastname		=	stripslashes( cbGetParam( $postdata, 'lastname', $user->lastname ) );
							if ( $lastname ) {
								$nameArr[]	=	$lastname;
							}
						}
						if ( count( $nameArr ) > 0 ) {
							$user->name			=	implode( ' ', $nameArr );
						}
					} else {
						// Form first/middle/last name from name if needed:
						$middleNamePos				=	strpos( $value, ' ' );
						$lastNamePos				=	strrpos( $value, ' ' );

						if ( $lastNamePos !== false ) {
							$user->firstname		=	substr( $value, 0, $middleNamePos );
							$user->lastname			=	substr( $value, ( $lastNamePos + 1 ) );

							if ( $middleNamePos !== $lastNamePos ) {
								$user->middlename	=	substr( $value, ( $middleNamePos + 1 ), ( $lastNamePos - $middleNamePos - 1 ) );
							} else {
								$user->middlename	=	'';
							}
						} else {
							$user->firstname		=	'';
							$user->middlename		=	'';
							$user->lastname			=	$value;
						}
					}

					$user->$col					=	$value;
				}
				break;

			default:
				$this->_setValidationError( $field, $user, $reason, sprintf(CBTxt::T( 'Unknown field %s' ), $field->name) );
				break;
		}
	}
	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string      $columnName  Column to validate
	 * @param  string      $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		global $_CB_framework, $_CB_database;

		$validated				=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		$fieldName				=	$field->name;

		if ( $validated && in_array( $fieldName, array( 'username', 'alias' ) ) ) {
			if ( $fieldName == 'alias' ) {
				$aliasReg		=	'/(^[^a-zA-Z])|[^a-zA-Z0-9\-]/';

				if ( $_CB_framework->getCfg( 'unicodeslugs' ) == 1 ) {
					$aliasReg	=	'/(^[^a-zA-Z\p{L}])|[^a-zA-Z0-9\-\p{L}]/u';
				}

				if ( is_numeric( $value ) || preg_match( $aliasReg, $value ) ) {
					// Profile urls should be strictly alphanumeric with or without dashes:
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_ALIAS', 'Please enter a valid alphanumeric profile url. Must begin with a letter and contain letters, numbers, and dashes only.' ) );
					return false;
				}

				if ( in_array( $value, Application::Router()->getViews() ) ) {
					// Profile urls can not match core views as it'd cause prefixed view parsing to kick in and unable to find the profile:
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_ALIAS_NOT_AVAILABLE', "The profile url '[alias]' is already in use.", array( '[alias]' => htmlspecialchars( $value ) ) ) );
					return false;
				}
			} elseif ( preg_match( '#[<>"\'%;()&\\\\]|\\.\\./#', $value ) ) {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_USERNAME', 'Please enter a valid username with no space at beginning or end and must not contain the following characters: < > \ " \' % ; ( ) &' ) );

				return false;
			}

			if ( ( $value !== '' ) && ( $value !== null ) ) {
				if ( is_null( $user->$fieldName ) || cbutf8_strtolower( trim( $value ) ) !== cbutf8_strtolower( trim( $user->$fieldName ) ) ) {
					// Username and Profile URL mut be unique and be unique to each other:
					$exists		=	$this->checkUsernameExists( $value );

					if ( ! $exists ) {
						$exists	=	$this->checkAliasExists( $value );
					}

					if ( $exists ) {
						if ( $fieldName == 'alias' ) {
							$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_ALIAS_NOT_AVAILABLE', "The profile url '[alias]' is already in use.", array( '[alias]' => htmlspecialchars( $value ) ) ) );
						} else {
							$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_USERNAME_NOT_AVAILABLE', "The username '[username]' is already in use.", array( '[username]' =>  htmlspecialchars( $value ) ) ) );
						}

						return false;
					}
				}
			}
		}

		return $validated;
	}

	/**
	 * Returns the minimum field length as set
	 *
	 * @param  FieldTable  $field
	 * @return int
	 */
	public function getMinLength( $field )
	{
		$min						=	parent::getMinLength( $field );

		if ( in_array( $field->name, array( 'username', 'alias' ) ) ) {
			if ( $min < 2 ) {
				$min				=	2;
			}
		}

		return $min;
	}

	/**
	 * Returns the maximum field length as set
	 *
	 * @param  FieldTable  $field
	 * @return int
	 */
	public function getMaxLength( $field )
	{
		$maxLen						=	parent::getMaxLength( $field );
		if ( $maxLen ) {
			return $maxLen;
		}
		if ( in_array( $field->name, array( 'username', 'alias' ) ) ) {
			return 150;
		} else {
			return 100;
		}
	}
}