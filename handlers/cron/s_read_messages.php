<?php
@ignore_user_abort(1);
@ini_set('memory_limit', '128M');
@ini_set('output_buffering', 'off');
@ini_set('max_execution_time', 0);
@set_time_limit(0);

load_class_local('c_juick');

class s_read_messages extends a_sub_handler_ra {

	protected $limit_users_messages=10;

	function delete_empty_messages(){
		$sql="DELETE FROM messages WHERE tags='' AND body='' AND date_import<CURDATE()";
		$this->db->db_query($sql);
	}

	function read_messages(){
		$this->delete_empty_messages();

		$sql="SELECT
						u.uid, max(m.mid) mid
					FROM users u
						LEFT JOIN messages m USING(uid)
					WHERE u.uid is NOT NULL
					GROUP BY u.uid
					ORDER BY m.date_import ASC
					LIMIT ".$this->limit_users_messages;
		$res=$this->db->get_array($sql);
		if(empty($res)){
			echo 'empty list.';
			return;
		}

		$o_juick=new c_juick();

		foreach ($res as $itm){
			$xml=$o_juick->make_xml_messages($itm['uid'], $itm['mid']);
			$res_juick=$o_juick->send($xml);
			$res_juick=$res_juick[0];
			//print_r($res_juick);

			$s_uid=tosql($itm['uid']);
			if (count($res_juick)==0) {
				//дабы без сообщений не обрабатывались вечно
				$sql="INSERT INTO messages
								(uid, mid, date, tags, body, date_import)
							VALUES
								($s_uid, 0, NOW(), '', '', NOW())
							ON DUPLICATE KEY UPDATE
								date=NOW(),
								tags='',
								body='',
								date_import=NOW()";
				$this->db->db_query($sql);
				continue;
			}

			$insert_lines=array();
			foreach ($res_juick as $line){
				if (empty($line['body']) && empty($line['tags'])){
					continue;
				}

				if ($line['pak_id']!=$itm['uid']){
					echo '  error on pak_id. We need "'.$itm['uid'].'" but we have "'.$line['pak_id'].'"'."\n<BR/>";
					echo '  result:'; print_r($o_juick->res);
					echo "\n<BR/>";
					echo '  last query:'; print_r($xml);
					echo "\n<BR/>";
					echo '  last ansver:'; print_r($o_juick->con->last_input_xml);
					echo "\n<BR/>";

					echo "\n<BR/>";
					echo '  CONTINUE!'."\n<BR/>";
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
	}

	function ajax_process(){
		$this->read_messages();
	}

}
