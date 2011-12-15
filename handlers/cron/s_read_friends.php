<?php
@ignore_user_abort(1);
@ini_set('memory_limit', '128M');
@ini_set('output_buffering', 'off');
@ini_set('max_execution_time', 0);
@set_time_limit(0);

load_class_local('c_juick');

class s_read_friends extends a_sub_handler_ra {

	function read_friends(){
		$sql="SELECT
						u.uid, f.date
					FROM users u
						LEFT JOIN friends f USING(uid)
					WHERE u.uid is NOT NULL
					GROUP BY u.uid
					HAVING (max(f.date)<DATE(NOW()) or f.date is NULL)";
		$res=$this->db->get_array($sql);
		if (empty($res)){
			echo 'empty list.';
			return;
		}

		$o_juick=new c_juick();

		foreach ($res as $itm){
			$xml=$o_juick->make_xml_friends($itm['uid']);
			$res_juick=$o_juick->send($xml);
			$count_friends=0;
			if ($res_juick[0]['pak_id']!=$itm['uid']){
				echo '  error on pak_id. We need "'.$itm['uid'].'" but we have "'.$res_juick[0]['pak_id'].'"'."\n<BR/>";
				echo '  result:'; print_r($o_juick->res);
				echo "\n<BR/>";
				echo '  last query:'; print_r($xml);
				echo "\n<BR/>";
				echo '  last ansver:'; print_r($o_juick->con->last_input_xml);
				echo "\n<BR/>";

				echo "\n<BR/>";
				$s_friends="''";
			}
			else {
				$count_friends=count($res_juick[0]['uids']);
				$s_friends=tosql(serialize($res_juick));
			}

			$count_subscribers=0;
			$xml=$o_juick->make_xml_subscribers($itm['uid']);
			$res_juick=$o_juick->send($xml);
			if ($res_juick[0]['pak_id']!=$itm['uid']){
				echo '  error on pak_id. We need "'.$itm['uid'].'" but we have "'.$res_juick[0]['pak_id'].'"'."\n<BR/>";
				echo '  result:'; print_r($o_juick->res);
				echo "\n<BR/>";
				echo '  last query:'; print_r($xml);
				echo "\n<BR/>";
				echo '  last ansver:'; print_r($o_juick->con->last_input_xml);
				echo "\n<BR/>";

				echo "\n<BR/>";
				$s_subscribers="''";
			}
			else {
				$count_subscribers=count($res_juick[0]['uids']);
				$s_subscribers=tosql(serialize($res_juick));
			}

			$s_uid=tosql($itm['uid']);

			if ($count_subscribers==0 && $count_friends==0){
				//нет ни подписчиков ни друзей. Может он дропнулся. Надо перечитать uid
				$sql="UPDATE users SET uid=NULL WHERE uid=$s_uid";
				$this->db->db_query($sql);
				//если он ещё и имя поменял, то это уже его проблемы
				//@TODO если он дропнулся, то можно и его статистику грохнуть, всё равно её уже не достать, т.к. uid у него тоже сменился
			}

			$sql="INSERT INTO friends
							(uid, date, friends, subscribers)
						VALUES
							($s_uid, DATE(NOW()), $s_friends, $s_subscribers)
						ON DUPLICATE KEY UPDATE
							friends=$s_friends,
							subscribers=$s_subscribers";
			$this->db->db_query($sql);
		}

		$o_juick->disconnect();

	}

	function ajax_process(){
		$this->read_friends();
	}

}