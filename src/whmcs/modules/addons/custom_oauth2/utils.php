<?php

function get_first_value_from_array($array, $property) {
	if (isset($array[$property])) {
		return $array[$property][0];
	}
	return null;
}