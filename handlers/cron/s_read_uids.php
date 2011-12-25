<?php
//@TODO too much debug information

@ignore_user_abort(1);
@ini_set('memory_limit', '128M');
@ini_set('output_buffering', 'off');
@ini_set('max_execution_time', 0);
@set_time_limit(0);

load_class_local('c_juick');

class s_read_uids extends a_sub_handler_ra {

	/**
	 *
	 * @var p_cron
	 */
	public $handler;
	protected $limit=100;

	protected $max_users_in_block=20;

	function delete_old_records(){
		$sql="DELETE FROM users
					WHERE TIMESTAMPDIFF(MONTH,last_date,first_date)>0 AND  (uid is NULL OR uname<>'')";
		$this->db->db_query($sql);
	}

	function is_valid_login($email) {
		if(preg_match("/^[@a-z0-9(йцукенгшщзхъэждлорпавыфячсмитьбюё)\._-]+/i", $email)) return TRUE;
		else return FALSE;
	}

	function test_login($uname){
		$s_uname=tosql($uname);
		if (!$this->is_valid_login($uname) || $uname[0]=='@' || mb_strpos($uname,'"')!==false  || mb_strpos($uname,"'")!==false){
			echo "DELETE not valid '$uname'\n";
			$sql="DELETE FROM users WHERE uname=$s_uname";
			$this->db->db_query($sql);
			return false;
		}
		return true;
	}

	function get_unames(){
		$sql="SELECT
						uname
					FROM users
					WHERE uid is NULL
						AND uname<>''
		      LIMIT ".$this->limit;
		$res=$this->db->get_array($sql);

		return $res;
	}

	function delete_unames($s_unames){
		$sql="DELETE FROM users WHERE uname IN (".implode(',',$s_unames).")";
		$this->db->db_query($sql);
	}

	function read_uids(){
		$this->delete_old_records();

		$res=$this->get_unames();

		if ($_REQUEST['debug_cron']==1){
			print_r($res);
		}
		if (empty($res)){
			echo 'empty list.';
			exit;
		}

		$block_users=array();
		$cur_block=0;
		$s_unames=array();
		$s_bad_names=array();
		foreach ($res as $itm){
			$uname=$itm['uname'];
			$s_uname=tosql($uname);

			if ($this->test_login($uname)){
				$block_users[$cur_block][]=$uname;
				if (count($block_users[$cur_block])>=$this->max_users_in_block){
				  $cur_block++;
				}
				$s_unames[$uname]=$s_uname;
		  }
		  else {
		  	$s_bad_names[]=$s_uname;
		  }
		}

		if (!empty($s_bad_names)){
			$this->delete_unames($s_bad_names);
		}

		if (empty($s_unames)){
			echo 'no valid logins.';
			exit;
		}

		$sql="UPDATE users
					SET last_date=NOW()
					WHERE uname IN (".implode(',',$s_unames).")";
		$this->db->db_query($sql);

		$o_juick=new c_juick();
		echo "START ";

		$out_xmls=array();
		foreach ($block_users as $block){
			$out_xmls[]=$o_juick->make_xml_uid_by_uname($block);
		}

	  $res=$o_juick->send_xmls($out_xmls);

		if (!is_array($res)){
			echo 'not array $res'."\n<BR/>";
			print_r($res);
			$this->handler->answer_error(0, $o_juick, array(), $out_xmls, false);
		}
		else {
			foreach ($res as $one_res){
				$uids=$one_res['uids'];
				$unames=$one_res['unames'];

				if (!is_array($unames)){
					echo 'not array $unames';
					echo "\n<BR/>";
					print_r($one_res);
					$this->handler->answer_error(0, $o_juick, array(), $out_xmls, false);
				}
				else {
					foreach ($unames as $k=>$cur_uname){
						$s_uname=tosql($cur_uname);
						$s_uid=tosql($uids[$k]);
						unset($s_unames[$cur_uname]);

						$sql="UPDATE users
						      SET uid=$s_uid
									WHERE uname=$s_uname";
						$this->db->db_query($sql);
					}

					if (!empty($s_unames)){
						//оставшиеся которые не разрезолвились
						$this->delete_unames($s_unames);
					}
				}
			}
		}
		$o_juick->disconnect();

		echo " END";
		exit;
	}

	function ajax_process(){
		$this->read_uids();
	}
}
