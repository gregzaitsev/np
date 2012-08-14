<?php

class ent_status extends sm_ent {

	public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, color
							from
								status
							where
								id = ?i(id)"
				,"binds" => array(
					"id" => $id
				)
			)
		);
		return $res["result"][0];
    }

	public function getList( ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, color
							from
								status"
				)
		);
		return $res["result"];
    }
}

?>
