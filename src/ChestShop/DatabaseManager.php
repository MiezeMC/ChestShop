<?php
declare(strict_types=1);
namespace ChestShop;

use pocketmine\block\BaseSign;
use pocketmine\block\Block;

class DatabaseManager
{
	private $database;

	public function __construct(string $path)
	{
		$this->database = new \SQLite3($path);
		$sql = "CREATE TABLE IF NOT EXISTS ChestShop(
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					shopOwner TEXT NOT NULL,
					saleNum INTEGER NOT NULL,
					price INTEGER NOT NULL,
					productID INTEGER NOT NULL,
					productMeta INTEGER NOT NULL,
					signX INTEGER NOT NULL,
					signY INTEGER NOT NULL,
					signZ INTEGER NOT NULL,
					chestX INTEGER NOT NULL,
					chestY INTEGER NOT NULL,
					chestZ INTEGER NOT NULL
		)";
		$this->database->exec($sql);
	}

	public function registerShop(string $shopOwner, int $saleNum, int $price, int $productID, int $productMeta, Block $sign, Block $chest) : bool
	{
		return $this->database->exec("INSERT OR REPLACE INTO ChestShop (id, shopOwner, saleNum, price, productID, productMeta, signX, signY, signZ, chestX, chestY, chestZ) VALUES
			((SELECT id FROM ChestShop WHERE signX = $sign->getPosition()->x AND signY = $sign->getPosition()->y AND signZ = $sign->getPosition()->z),
			'$shopOwner', $saleNum, $price, $productID, $productMeta, $sign->getPosition()->x, $sign->getPosition()->y, $sign->getPosition()->z, $chest->getPosition()->x, $chest->getPosition()->y, $chest->getPosition()->z)");
	}

	public function selectByCondition(array $condition) : bool|\SQLite3Result
	{
		$where = $this->formatCondition($condition);
		$res = false;
		try{
			$res = $this->database->query("SELECT * FROM ChestShop WHERE $where");
		}finally{
			return $res;
		}
	}

	/**
	 * @param array $condition
	 * @return bool
	 */
	public function deleteByCondition(array $condition) : bool
	{
		$where = $this->formatCondition($condition);
		return $this->database->exec("DELETE FROM ChestShop WHERE $where");
	}

	private function formatCondition(array $condition) : string
	{
		$result = "";
		$first = true;
		foreach ($condition as $key => $val) {
			if ($first) $first = false;
			else $result .= "AND ";
			$result .= "$key = $val ";
		}
		return trim($result);
	}
} 