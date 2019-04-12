<?php declare(strict_types=1);

namespace SalesChannelTeam\Plugin\InstagramShopping\Migration;

use Shopware\Core\Framework\Migration\MigrationStep;
use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use SalesChannelTeam\Plugin\InstagramShopping\InstagramShopping as Instagram;


class Migration1554708925Config extends MigrationStep
{
    /**
     * get creation timestamp
     */
    public function getCreationTimestamp(): int
    {
        return 1554708925;
    }

    /**
     * update non-destructive changes
     */
    public function update(Connection $connection): void
    {
        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'namespace' => Instagram::CONFIG_NAMESPACE,
            'configuration_key' => 'catalog_id',
            'configuration_value' => '2301606873237604',
            'created_at' => date(Defaults::STORAGE_DATE_FORMAT)
        ]);

        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'namespace' => Instagram::CONFIG_NAMESPACE,
            'configuration_key' => 'access_token',
            'configuration_value' => 'EAAF3cZAHi4fMBAFltokx4HWjLpvg2sozOcXnnON3znKcFe1xeAFyVuBhLd98tOYyCgOYGunTRscWlx4unun5HJhtUOXBUmUhHZCAfHqW7l8IPpPVvZASms0Vc6cM2X6LeBTZBoLniVHDsE9oqfkEzJJuCh0pRaORg5nZCG4sB2FtdFO0FJGBu',
            'created_at' => date(Defaults::STORAGE_DATE_FORMAT)
        ]);





    }

    /**
     * update destructive changes
     */
    public function updateDestructive(Connection $connection): void
    {
    }
}
