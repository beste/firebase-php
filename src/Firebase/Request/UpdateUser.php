<?php

declare(strict_types=1);

namespace Kreait\Firebase\Request;

use Beste\Json;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Request;
use Stringable;

use function array_reduce;
use function array_unique;
use function is_array;
use function is_string;
use function mb_strtolower;
use function preg_replace;

final class UpdateUser implements Request
{
    /** @phpstan-use EditUserTrait<self> */
    use EditUserTrait;

    public const DISPLAY_NAME = 'DISPLAY_NAME';

    public const PHOTO_URL = 'PHOTO_URL';

    public const EMAIL = 'EMAIL';

    /**
     * @var list<non-empty-string>
     */
    private array $attributesToDelete = [];

    /**
     * @var list<non-empty-string>
     */
    private array $providersToDelete = [];

    /**
     * @var array<string, mixed>|null
     */
    private ?array $customAttributes = null;

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param array<string, mixed> $properties
     *
     * @throws InvalidArgumentException when invalid properties have been provided
     */
    public static function withProperties(array $properties): self
    {
        $request = self::withEditableProperties(new self(), $properties);

        foreach ($properties as $key => $value) {
            switch (mb_strtolower((string) preg_replace('/[^a-z]/i', '', $key))) {
                case 'deletephoto':
                case 'deletephotourl':
                case 'removephoto':
                case 'removephotourl':
                    $request = $request->withRemovedPhotoUrl();

                    break;

                case 'deletedisplayname':
                case 'removedisplayname':
                    $request = $request->withRemovedDisplayName();

                    break;

                case 'deleteemail':
                case 'removeemail':
                    $request = $request->withRemovedEmail();

                    break;

                case 'deleteattribute':
                case 'deleteattributes':
                    foreach ((array) $value as $deleteAttribute) {
                        if (!is_string($deleteAttribute)) {
                            continue;
                        }

                        if ($deleteAttribute === '') {
                            continue;
                        }

                        $deleteAttribute = preg_replace('/[^a-z]/i', '', $deleteAttribute);

                        if ($deleteAttribute === null) {
                            continue;
                        }

                        switch (mb_strtolower($deleteAttribute)) {
                            case 'displayname':
                                $request = $request->withRemovedDisplayName();

                                break;

                            case 'photo':
                            case 'photourl':
                                $request = $request->withRemovedPhotoUrl();

                                break;

                            case 'email':
                                $request = $request->withRemovedEmail();

                                break;
                        }
                    }

                    break;

                case 'customattributes':
                case 'customclaims':
                    $request = $request->withCustomAttributes($value);

                    break;

                case 'phonenumber':
                case 'phone':
                    if (in_array($value, [false, null, ''], true)) {
                        $request = $request->withRemovedPhoneNumber();
                    }

                    break;

                case 'deletephone':
                case 'deletephonenumber':
                case 'removephone':
                case 'removephonenumber':
                    $request = $request->withRemovedPhoneNumber();

                    break;

                case 'deleteprovider':
                case 'deleteproviders':
                case 'removeprovider':
                case 'removeproviders':
                    $request = array_reduce(
                        (array) $value,
                        static fn(self $request, $provider): UpdateUser => $request->withRemovedProvider($provider),
                        $request,
                    );

                    break;

                case 'resetmultifactor':
                    if ($value === true) {
                        $request = $request->resetMultiFactor();
                    }

                    break;

                case 'multifactors':
                    $request = $request->withMultiFactors($value);

                    break;
            }
        }

        return $request;
    }

    public function withRemovedPhoneNumber(): self
    {
        $request = clone $this;
        $request->phoneNumber = null;

        return $request->withRemovedProvider('phone');
    }

    /**
     * @param Stringable|string $provider
     */
    public function withRemovedProvider($provider): self
    {
        $providerString = trim((string) $provider);

        if ($providerString === '') {
            return $this;
        }

        $request = clone $this;
        $request->providersToDelete[] = $providerString;

        return $request;
    }

    public function withRemovedDisplayName(): self
    {
        $request = clone $this;
        $request->displayName = null;
        $request->attributesToDelete[] = self::DISPLAY_NAME;

        return $request;
    }

    public function withRemovedPhotoUrl(): self
    {
        $request = clone $this;
        $request->photoUrl = null;
        $request->attributesToDelete[] = self::PHOTO_URL;

        return $request;
    }

    public function withRemovedEmail(): self
    {
        $request = clone $this;
        $request->email = null;
        $request->attributesToDelete[] = self::EMAIL;

        return $request;
    }

    /**
     * @param array<array-key, array{
     *     'mfaEnrollmentId'?: string,
     *     'displayName': string,
     *     'phoneInfo': string,
     *     'enrolledAt'?: string,
     * }> $enrollments
     */
    public function withMultiFactors(array $enrollments): self
    {
        $request = clone $this;
        $request->multiFactor ??= [];
        $request->multiFactor['enrollments'] = $enrollments;

        return $request;
    }

    public function resetMultiFactor(): self
    {
        $request = clone $this;
        $request->multiFactor ??= [];
        $request->multiFactor['enrollments'] = [];

        return $request;
    }

    /**
     * @param array<string, mixed> $customAttributes
     */
    public function withCustomAttributes(array $customAttributes): self
    {
        $request = clone $this;
        $request->customAttributes = $customAttributes;

        return $request;
    }

    /**
     * @return array{
     *     customAttributes?: string,
     *     deleteAttribute?: list<non-empty-string>,
     *     deleteProvider?: list<non-empty-string>,
     * }
     */
    public function jsonSerialize(): array
    {
        if (!$this->hasUid()) {
            throw new InvalidArgumentException('A uid is required to update an existing user.');
        }

        $data = $this->prepareJsonSerialize();

        if (is_array($this->customAttributes)) {
            $data['customAttributes'] = $this->customAttributes === [] ? '{}' : Json::encode($this->customAttributes);
        }

        if ($this->attributesToDelete !== []) {
            $data['deleteAttribute'] = array_unique($this->attributesToDelete);
        }

        if ($this->providersToDelete !== []) {
            $data['deleteProvider'] = $this->providersToDelete;
        }

        return $data;
    }
}
