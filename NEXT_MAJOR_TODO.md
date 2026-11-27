# Next Major TODO

## App Check Replay Protection

- Context: `Kreait\Firebase\Contract\AppCheckWithReplayProtection` was introduced as a transitional contract
  to preserve backwards compatibility in the current major release.
- Fold replay protection into `Kreait\Firebase\Contract\AppCheck::verifyToken()` (e.g. argument/options),
  and remove the transitional `Kreait\Firebase\Contract\AppCheckWithReplayProtection` contract.
- Update docs and integration tests accordingly after the API consolidation.

## App Check Debug Tokens

- Context: `Kreait\Firebase\Contract\AppCheckWithDebugTokens` was introduced as a transitional contract
  to preserve backwards compatibility in the current major release.
- Move its methods into `Kreait\Firebase\Contract\AppCheck` and remove the transitional contract.
