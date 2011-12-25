<?php
@ignore_user_abort(1);
@ini_set('memory_limit', '128M');
@ini_set('output_buffering', 'off');
@ini_set('max_execution_time', 0);
@set_time_limit(0);

load_class_local('c_juick');

class s_read_messages extends a_sub_handler_ra {

	/**
	 *
	 * @var p_cron
	 */
	public $handler;

	protected $limit=20;

	function delete_empty_messages(){
		$sql="DELETE FROM messages WHERE tags='' AND body='' AND date_import<CURDATE()";
		$this->db->db_query($sql);
	}

	function get_uids(){
		/*$sql="SELECT
						u.uid, max(m.mid) mid
					FROM users u
						LEFT JOIN messages m USING(uid)
					WHERE u.uid is NOT NULL
					GROUP BY u.uid
					ORDER BY m.date_import ASC
					LIMIT ".$this->limit;*/

		$sql="SELECT
		 				u.uid, max(m.mid) mid
					FROM users u
					  LEFT JOIN messages m USING(uid)
					WHERE u.uid is NOT NULL
					GROUP BY u.uid
					ORDER BY u.last_messages
					LIMIT ".$this->limit;

		$res=$this->db->get_array($sql);
		return $res;
	}

	function read_messages(){
		$this->delete_empty_messages();
		$res=$this->get_uids();

		if(empty($res)){
			echo 'empty list.';
			exit;
		}

		//print_r($res);

		$o_juick=new c_juick();
		$s_uids=array();

		echo "START ";
		foreach ($res as $itm){
			$uid=$itm['uid'];
			$s_uid=tosql($uid);
			$s_uids[]=$s_uid;

			$xml=$o_juick->make_xml_messages($uid, $itm['mid']);
			$res_juick=$o_juick->send($xml);
			$res_juick=$res_juick[0];
			//print_r($res_juick);

			if (count($res_juick)==0) {
				//дабы без сообщений не обрабатывались вечно
				/*$sql="INSERT INTO messages
								(uid, mid, date, tags, body, date_import)
							VALUES
								($s_uid, 0, NOW(), '', '', NOW())
							ON DUPLICATE KEY UPDATE
								date=NOW(),
								tags='',
								body='',
								date_import=NOW()";
				$this->db->db_query($sql);*/
				continue;
			}

			$insert_lines=array();
			foreach ($res_juick as $line){
				if (empty($line['body']) && empty($line['tags'])){
					continue;
				}

				if ($line['pak_id']!=$uid){
					$this->handler->answer_error($uid, $o_juick, $res_juick, $xml, false);
					continue;
				}

				$s_mid=tosql($line['mid']);
				$s_date=tosql($line['date']);
				if (!empty($line['tags'])){
					$s_tags=tosql(implode(', ',$line['tags']));
				}
				else {
					$s_tags="''";
				}

				$s_body=$line['body'];

				if (strlen($s_body)>255) $s_body=substr($s_body,0,255-3).'...';
				$s_body=tosql($s_body);

				$s_line="($s_uid, $s_mid, $s_date, $s_tags, $s_body, NOW())";
				$insert_lines[]=$s_line;
			}

			$sql="INSERT IGNORE INTO messages
							(uid, mid, date, tags, body, date_import)
						VALUES
							".implode(',',$insert_lines);
			$this->db->db_query($sql);
		}

		$o_juick->disconnect();

		$s_uids=implode(',',$s_uids);
		$sql="UPDATE users
						SET last_messages=NOW()
					WHERE uid IN ($s_uids)";
		//echo " $sql ";
		$this->db->db_query($sql);

		echo " END";
		exit;
	}

	function ajax_process(){
		$this->read_messages();
	}

}
