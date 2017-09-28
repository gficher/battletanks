<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DomainController {
	private $email, $privatekey, $zone;

	public function setKey($email, $privatekey) {
		$this->email = $email;
		$this->privatekey = $privatekey;
	}

	public function setZone($zone) {
		$this->zone = $zone;
	}

	public function getZones() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones");

		$data = json_encode(array(
			"per_page" => "100"
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"X-Auth-Key: $this->privatekey",
			"X-Auth-Email: $this->email",
			"Content-Type: application/json",
		));

		$output = curl_exec($ch);

		if(curl_errno($ch)){
			return false;
		}

		$output = json_decode($output, true);

		$result = Array();
		if ($output['result_info']['total_pages'] != 1) {
			for($i = 2; $i <= $output['result_info']['total_pages']; $i++) {
				$data = json_encode(array(
					"page" => $i,
				));

				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

				$pOutput = curl_exec($ch);

				if(curl_errno($ch)){
					return false;
				}

				$pOutput = json_decode($pOutput, true);
				foreach ($pOutput['result'] as $value) {
					$result[] = $value;
				}
			}
		}

		curl_close($ch);

		return $result;
	}

	public function getZoneId($zoneName) {
		foreach($this->getZones() as $key => $value) {
			if ($zoneName == $value['name']) {
				return $value['id'];
			}
		}
		return false;
	}

	public function listRecords($type = null, $name = null, $content = null, $order = "type", $direction = "desc", $match = "all") {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$this->zone/dns_records");

		$data = json_encode(array(
			"per_page" => "100",
			"type" => $type,
			"name" => $name,
			"content" => $content,
			"order" => $order,
			"direction" => $direction,
			"match" => $match,
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"X-Auth-Key: $this->privatekey",
			"X-Auth-Email: $this->email",
			"Content-Type: application/json",
		));

		$output = curl_exec($ch);

		if(curl_errno($ch)){
			return false;
		}

		$output = json_decode($output, true);
		$result = Array();

		if ($output['result_info']['total_pages'] != 1) {
			for($i = 2; $i <= $output['result_info']['total_pages']; $i++) {
				$data = json_encode(array(
					"page" => $i,
				));

				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

				$pOutput = curl_exec($ch);

				if(curl_errno($ch)){
					return false;
				}

				$pOutput = json_decode($pOutput, true);
				foreach ($pOutput['result'] as $value) {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	public function getRecords() {
		return $this->listRecords();
	}

	public function getRecord($id) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$this->zone/dns_records/$id");

		$data = json_encode(array(
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"X-Auth-Key: $this->privatekey",
			"X-Auth-Email: $this->email",
			"Content-Type: application/json",
		));

		$output = curl_exec($ch);

		if(curl_errno($ch)){
			$output = "{\"success\": false}";
		}

		$output = json_decode($output, true);

		curl_close($ch);

		return $output;
	}

	public function getRecordId($recordName) {
		foreach($this->getRecords() as $value) {
			if ($recordName == $value['name']) {
				return $value['id'];
			}
		}
		return false;
	}

	public function createRecord($type, $name, $content, $ttl = 1, $proxied = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$this->zone/dns_records");

		$data = json_encode(array(
			"type" => $type,
			"name" => $name,
			"content" => $content,
			"ttl" => $ttl,
			"proxied" => $proxied,
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"X-Auth-Key: $this->privatekey",
			"X-Auth-Email: $this->email",
			"Content-Type: application/json",
		));

		$output = curl_exec($ch);

		if(curl_errno($ch)){
			$output = "{\"success\": false}";
		}

		$output = json_decode($output, true);

		curl_close($ch);

		return $output;
	}

	public function updateRecord($recordId, $type, $name, $content, $ttl = null, $proxied = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$this->zone/dns_records/$recordId");

		$data = array(
			"type" => $type,
			"name" => $name,
			"content" => $content,
		);

		if ($ttl)
			$data["ttl"] = $ttl;
		if ($proxied)
			$data["proxied"] = $proxied;

		$data = json_encode($data);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"X-Auth-Key: $this->privatekey",
			"X-Auth-Email: $this->email",
			"Content-Type: application/json",
		));

		$output = curl_exec($ch);

		if(curl_errno($ch)){
			$output = "{\"success\": false}";
		}

		$output = json_decode($output, true);

		curl_close($ch);

		return $output;
	}
	public function deleteRecord($recordId) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$this->zone/dns_records/$recordId");

		$data = json_encode(array());

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"X-Auth-Key: $this->privatekey",
			"X-Auth-Email: $this->email",
			"Content-Type: application/json",
		));

		$output = curl_exec($ch);

		if(curl_errno($ch)){
			$output = "{\"success\": false}";
		}

		$output = json_decode($output, true);

		curl_close($ch);

		return $output;
	}
}
