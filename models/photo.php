<?php
class Photo extends Model
{
	public function toJSON(){
		return json_encode($this->as_array());	
	}
}
?>