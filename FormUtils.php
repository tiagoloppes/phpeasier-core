<?php
class FormUtils {
	public static function getSelectbox(array $arrayObject, $selectedObject, $keyColumn, $labelColumn, $inputName, $cssClass = '', $id = NULL, $required = false, $multiple = false) {
		$id = $id != NULL ? $id : $inputName;
		$str = '<select ';
		if ($cssClass)
			$str .= 'class="' . $cssClass . '" id="' . $id . '" name="' . $inputName . '"';
		if ($required == true)
			$str .= ' required="required"';
		if ($multiple == true)
			$str .= ' multiple="multiple"';
		$str .= '>' . "\n";
		$option = '';
		$selected = false;
		foreach ( $arrayObject as $obj ) {
			$option .= '<option ';
			if ($obj->{$keyColumn} == $selectedObject->{$keyColumn}) {
				$option .= 'selected="selected" ';
				$selected = true;
			}
			$label = $obj->{$labelColumn};
			$option .= 'value="' . $obj->{$keyColumn} . '">' . $label . "</option>\n";
		}
		if ($required == true)
			$str .= '<option value="">Selecione...</option>' . "\n" . $option;
		else
			$str .= '<option value="">Nenhum</option>' . "\n" . $option;
		$str .= $option;
		$str .= '</select>' . "\n";
		return $str;
	}
}
