<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\ApiAccount
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property bool $actif
 * @property string|null $token
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereUsername($value)
 * @mixin \Eloquent
 */
	class IdeHelperApiAccount {}
}

namespace App\Models{
/**
 * App\Models\Champion
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $img_url
 * @property string $champion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $matches
 * @property-read int|null $matches_count
 * @method static \Illuminate\Database\Eloquent\Builder|Champion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Champion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Champion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereChampionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperChampion {}
}

namespace App\Models{
/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $img_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperItem {}
}

namespace App\Models{
/**
 * App\Models\ItemSummonerMatch
 *
 * @property int $item_id
 * @property int $summoner_match_id
 * @property int $position
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch whereSummonerMatchId($value)
 * @mixin \Eloquent
 */
	class IdeHelperItemSummonerMatch {}
}

namespace App\Models{
/**
 * App\Models\Map
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Map newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map query()
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperMap {}
}

namespace App\Models{
/**
 * App\Models\Matche
 *
 * @property int $id
 * @property int $updated
 * @property string $match_id
 * @property int|null $mode_id
 * @property int|null $map_id
 * @property int|null $queue_id
 * @property string|null $match_creation
 * @property string|null $match_duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $since_match_end
 * @property-read \App\Models\Map|null $map
 * @property-read \App\Models\Mode|null $mode
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\Queue|null $queue
 * @method static Builder|Matche filters($filters)
 * @method static Builder|Matche newModelQuery()
 * @method static Builder|Matche newQuery()
 * @method static Builder|Matche query()
 * @method static Builder|Matche whereCreatedAt($value)
 * @method static Builder|Matche whereId($value)
 * @method static Builder|Matche whereMapId($value)
 * @method static Builder|Matche whereMatchCreation($value)
 * @method static Builder|Matche whereMatchDuration($value)
 * @method static Builder|Matche whereMatchId($value)
 * @method static Builder|Matche whereModeId($value)
 * @method static Builder|Matche whereQueueId($value)
 * @method static Builder|Matche whereUpdated($value)
 * @method static Builder|Matche whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperMatche {}
}

namespace App\Models{
/**
 * App\Models\Mode
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperMode {}
}

namespace App\Models{
/**
 * App\Models\Queue
 *
 * @property int $id
 * @property string $map
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperQueue {}
}

namespace App\Models{
/**
 * App\Models\Summoner
 *
 * @property int $id
 * @property string|null $summoner_id
 * @property string|null $account_id
 * @property string|null $puuid
 * @property string|null $name
 * @property string|null $profile_icon_id
 * @property string|null $revision_date
 * @property string|null $summoner_level
 * @property string|null $last_scanned_match
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $auto_update
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $matches
 * @property-read int|null $matches_count
 * @method static Builder|Summoner newModelQuery()
 * @method static Builder|Summoner newQuery()
 * @method static Builder|Summoner query()
 * @method static Builder|Summoner whereAccountId($value)
 * @method static Builder|Summoner whereAutoUpdate($value)
 * @method static Builder|Summoner whereCreatedAt($value)
 * @method static Builder|Summoner whereId($value)
 * @method static Builder|Summoner whereLastScannedMatch($value)
 * @method static Builder|Summoner whereName($value)
 * @method static Builder|Summoner whereProfileIconId($value)
 * @method static Builder|Summoner wherePuuid($value)
 * @method static Builder|Summoner whereRevisionDate($value)
 * @method static Builder|Summoner whereSummonerId($value)
 * @method static Builder|Summoner whereSummonerLevel($value)
 * @method static Builder|Summoner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ApiAccount[] $lolApiAccounts
 * @property-read int|null $lol_api_accounts_count
 */
	class IdeHelperSummoner {}
}

namespace App\Models{
/**
 * App\Models\SummonerApi
 *
 * @property-read \App\Models\ApiAccount|null $lolApiAccount
 * @property-read \App\Models\Summoner|null $summoner
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi query()
 * @mixin \Eloquent
 */
	class IdeHelperSummonerApi {}
}

namespace App\Models{
/**
 * App\Models\SummonerLeague
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague query()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperSummonerLeague {}
}

namespace App\Models{
/**
 * App\Models\SummonerMatch
 *
 * @property int $id
 * @property int $won
 * @property float $kill_participation
 * @property float $kda
 * @property int $assists
 * @property int $deaths
 * @property int $kills
 * @property int $champ_level
 * @property array|null $challenges
 * @property array $stats
 * @property int $minions_killed
 * @property int $largest_killing_spree
 * @property int $champion_id
 * @property int $summoner_id
 * @property int $match_id
 * @property int $double_kills
 * @property int $triple_kills
 * @property int $quadra_kills
 * @property int $penta_kills
 * @property-read \App\Models\Champion|null $champion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Matche|null $match
 * @property-read \App\Models\Summoner|null $summoner
 * @method static Builder|SummonerMatch championsCalc($championIds)
 * @method static Builder|SummonerMatch filters($filters)
 * @method static Builder|SummonerMatch newModelQuery()
 * @method static Builder|SummonerMatch newQuery()
 * @method static Builder|SummonerMatch query()
 * @method static Builder|SummonerMatch whereAssists($value)
 * @method static Builder|SummonerMatch whereChallenges($value)
 * @method static Builder|SummonerMatch whereChampLevel($value)
 * @method static Builder|SummonerMatch whereChampionId($value)
 * @method static Builder|SummonerMatch whereDeaths($value)
 * @method static Builder|SummonerMatch whereDoubleKills($value)
 * @method static Builder|SummonerMatch whereId($value)
 * @method static Builder|SummonerMatch whereKda($value)
 * @method static Builder|SummonerMatch whereKillParticipation($value)
 * @method static Builder|SummonerMatch whereKills($value)
 * @method static Builder|SummonerMatch whereLargestKillingSpree($value)
 * @method static Builder|SummonerMatch whereMatchId($value)
 * @method static Builder|SummonerMatch whereMinionsKilled($value)
 * @method static Builder|SummonerMatch wherePentaKills($value)
 * @method static Builder|SummonerMatch whereQuadraKills($value)
 * @method static Builder|SummonerMatch whereStats($value)
 * @method static Builder|SummonerMatch whereSummonerId($value)
 * @method static Builder|SummonerMatch whereTripleKills($value)
 * @method static Builder|SummonerMatch whereWon($value)
 * @mixin \Eloquent
 */
	class IdeHelperSummonerMatch {}
}

namespace App\Models{
/**
 * App\Models\Tier
 *
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string|null $img_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperTier {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * App\Models\Version
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Version newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Version newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Version query()
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperVersion {}
}

