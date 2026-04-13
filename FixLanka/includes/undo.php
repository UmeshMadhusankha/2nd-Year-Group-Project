<?php

/**
 * Short Undo Window helpers (action_undo table)
 *
 * This is intentionally small and dependency-free so it can be used in APIs/controllers.
 */

function undo_window_seconds(): int
{
    // User requested a very short grace period ("30 second may be").
    return 30;
}

function undo_create(PDO $pdo, string $entityType, int $entityId, string $actionKey, array $meta, ?int $createdBy, ?string $createdRole, ?int $windowSeconds = null): array
{
    $secs = $windowSeconds ?? undo_window_seconds();
    $undoUntil = (new DateTime('now'))->modify('+' . (int)$secs . ' seconds')->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare(
        "INSERT INTO action_undo (entity_type, entity_id, action_key, meta_json, undo_until, used, used_at, used_by, used_role)
         VALUES (:t, :id, :k, :m, :u, 0, NULL, NULL, NULL)"
    );

    $stmt->execute([
        ':t' => $entityType,
        ':id' => $entityId,
        ':k' => $actionKey,
        ':m' => !empty($meta) ? json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
        ':u' => $undoUntil
    ]);

    return [
        'undo_id' => (int)$pdo->lastInsertId(),
        'undo_until' => $undoUntil,
        'undo_seconds' => (int)$secs
    ];
}

function undo_get_active(PDO $pdo, string $entityType, int $entityId, string $actionKey): ?array
{
    $stmt = $pdo->prepare(
        "SELECT undo_id, entity_type, entity_id, action_key, meta_json, undo_until, used,
                TIMESTAMPDIFF(SECOND, NOW(), undo_until) AS seconds_remaining
         FROM action_undo
         WHERE entity_type = :t AND entity_id = :id AND action_key = :k
           AND used = 0 AND undo_until > NOW()
         ORDER BY undo_id DESC
         LIMIT 1"
    );

    $stmt->execute([':t' => $entityType, ':id' => $entityId, ':k' => $actionKey]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;

    $row['undo_id'] = (int)$row['undo_id'];
    $row['entity_id'] = (int)$row['entity_id'];
    $row['used'] = (int)$row['used'];
    $row['seconds_remaining'] = max(0, (int)($row['seconds_remaining'] ?? 0));

    if (!empty($row['meta_json'])) {
        $meta = json_decode($row['meta_json'], true);
        $row['meta'] = is_array($meta) ? $meta : [];
    } else {
        $row['meta'] = [];
    }

    return $row;
}

function undo_mark_used(PDO $pdo, int $undoId, ?int $usedBy, ?string $usedRole): void
{
    $stmt = $pdo->prepare(
        "UPDATE action_undo
         SET used = 1, used_at = NOW(), used_by = :by, used_role = :role
         WHERE undo_id = :id AND used = 0"
    );
    $stmt->execute([
        ':by' => $usedBy,
        ':role' => $usedRole,
        ':id' => $undoId
    ]);
}
