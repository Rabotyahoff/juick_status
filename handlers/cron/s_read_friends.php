<?php
@ignore_user_abort(1);
@ini_set('memory_limit', '128M');
@ini_set('output_buffering', 'off');
@ini_set('max_execution_time', 0);
@set_time_limit(0);

load_class_local('c_juick');

class s_read_friends extends a_sub_handler_ra {

	/**
	 *
	 * @var p_cron
	 */
	public $handler;
	protected $limit=50;


	function get_uids(){
		/*$sql="SELECT
						u.uid, f.date
					FROM users u
						LEFT JOIN friends f USING(uid)
					WHERE u.uid is NOT NULL
					GROUP BY u.uid
					HAVING (max(f.date)<DATE(NOW()) or f.date is NULL)";*/
		$sql="SELECT
		        u.uid
					FROM users u
					WHERE u.uid is NOT NULL
					ORDER BY u.last_friends
					LIMIT ".$this->limit;
		$res=$this->db->get_array($sql);

		//$res=array();$res[]=array('uid'=>2);
		return $res;
	}

	function read_friends(){
		$res=$this->get_uids();
		if (empty($res)){
			echo 'empty list.';
			exit;
		}

		$o_juick=new c_juick();
		$s_uids=array();

		echo "START ";
		foreach ($res as $itm){
			$uid=$itm['uid'];
			$s_uid=tosql($uid);
			$s_uids[]=$s_uid;

			$xml=$o_juick->make_xml_friends($uid);
			$res_juick=$o_juick->send($xml);
			$count_friends=0;
			if ($res_juick[0]['pak_id']!=$uid){
				if ($this->handler->answer_error($uid, $o_juick, $res_juick, $xml)) continue;
				else $s_friends="''";
			}
			else {
				$count_friends=count($res_juick[0]['uids']);
				$s_friends=tosql(serialize($res_juick));
			}

			$count_subscribers=0;
			$xml=$o_juick->make_xml_subscribers($uid);
			$res_juick=$o_juick->send($xml);
			if ($res_juick[0]['pak_id']!=$uid){
				if ($this->handler->answer_error($uid, $o_juick, $res_juick, $xml)) continue;
				else $s_subscribers="''";
			}
			else {
				$count_subscribers=count($res_juick[0]['uids']);
				$s_subscribers=tosql(serialize($res_juick));
			}

			if ($count_subscribers==0 && $count_friends==0){
				//нет ни подписчиков ни друзей. Может он дропнулся. Надо перечитать uid
				$sql="UPDATE users SET uid=NULL WHERE uid=$s_uid";
				$this->db->db_query($sql);
				//если он ещё и имя поменял, то это уже его проблемы
				//@TODO если он дропнулся, то можно и его статистику грохнуть, всё равно её уже не достать, т.к. uid у него тоже сменился
				//а ели нет тогда что?
			}

			$sql="INSERT INTO friends
							(uid, date, friends, subscribers)
						VALUES
							($s_uid, CURDATE(), $s_friends, $s_subscribers)
						ON DUPLICATE KEY UPDATE
							friends=$s_friends,
							subscribers=$s_subscribers";
			$this->db->db_query($sql);

			//чтобы можно было получить данные на сегодня, если за предыдущий день ничего нет
			$sql="INSERT IGNORE INTO friends
							(uid, date, friends, subscribers)
						VALUES
							($s_uid, SUBDATE(CURDATE(),INTERVAL 1 DAY), $s_friends, $s_subscribers)";
			$this->db->db_query($sql);
		}
		$o_juick->disconnect();

		$s_uids=implode(',',$s_uids);
		$sql="UPDATE users
		      	SET last_friends=NOW()
		      WHERE uid IN ($s_uids)";
		$this->db->db_query($sql);

		echo " END";
		exit;
	}

	function ajax_process(){
		$this->read_friends();
	}

}