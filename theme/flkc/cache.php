<?php
// 缓存操作类
class Cache {
	// 初始化缓存
	public function __construct() {
		if ( !is_dir(__DIR__ . '/cache'))
			mkdir(__DIR__ . '/cache', 0755, true);
		return $this;
	}

	// 清空缓存
	public function clear() {
		$files = (array) glob(__DIR__ . '/cache/*');
		foreach ( $files as $path ) {
			if ( is_dir($path) ) {
				array_map('unlink', glob($path . '/*.php'));
				rmdir($path);
			} else unlink($path);
		}
		return true;
	}

	// 缓存是否存在
	public function has($name) {
		return $this->get($name) ? true : false;
	}

	// 获取缓存
	public function get($name) {
		$filename = $this->getCacheKey($name);
		if ( !is_file($filename) ) return false;
		$content = file_get_contents($filename);
		if ( false !== $content ) {
			$expire = (int) substr($content, 8, 12);
			if ( 0 !== $expire && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $expire ) return false;
			$content = substr($content, 32);
			$content = unserialize($content);
			return $content;
		}
		return false;
	}

	// 删除缓存
	public function rm($name) {
		$filename = $this->getCacheKey($name);
		return $this->unlink($filename);
	}

	// 缓存不存在则写入缓存
	public function remember($name, $value) {
		if ( !$this->has($name) ) {
			$time = time();
			while ( $time + 5 > $time && $this->has($name . '_lock') ) usleep(200000);
			try {
				$this->set($name . '_lock', true);
				if ( $value instanceof Closure) $value = call_user_func($value);
				$this->set($name, $value);
				$this->rm($name . '_lock');
			} catch(Exception $e) {
				$this->rm($name . '_lock');
				throw $e;
			} catch(throwable $e) {
				$this->rm($name . '_lock');
				throw $e;
			}
		} else $value = $this->get($name);

		return $value;
	}

	// 写入缓存
	public function set($name, $value, $expire = null) {
		if ( is_null($expire) ) $expire = 0;
		if ( $expire instanceof DateTime ) $expire = $expire->getTimestamp() - time();
		$filename = $this->getCacheKey($name);
		if ( !is_file($filename) ) $first = true;
		$data = serialize($value);
		$data = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
		$result = file_put_contents($filename, $data);
		if ( $result ) {
			clearstatcache();
			return true;
		}
		return false;
	}

	// 获取存储文件名
	private function getCacheKey($name) {
		$name = md5($name);
		$filename = __DIR__ . '/cache/' . $name . '.php';
		if ( !is_dir(__DIR__ . '/cache/') )
			mkdir(__DIR__ . '/cache/', 0755, true);
		return $filename;
	}

	// 删除文件
	private function unlink($path) {
		return is_file($path) && unlink($path);
	}
}