<?php
declare(strict_types=1);

namespace App\Models;

final class TransparencyWallet extends Model
{
    protected string $table = 'transparency_wallets';

    protected array $fillable = [
        'label',
        'wallet_address',
        'sort_order',
    ];
}
