<?php
// Shared helper for rendering "staticcontent" sections on customer-facing pages.
// - Normal visitors see only Published rows.
// - Moderators/Admins can preview Draft via ?sc_preview=1&sc_mode=draft

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function fixlanka_static_content_is_privileged(): bool
{
    $role = (string)($_SESSION['user_role'] ?? '');
    return in_array($role, ['moderator', 'admin'], true);
}

function fixlanka_static_content_include_draft_for_request(): bool
{
    $isPreview = (string)($_GET['sc_preview'] ?? '') === '1';
    $mode = strtolower(trim((string)($_GET['sc_mode'] ?? '')));

    if (!$isPreview) {
        return false;
    }

    if (!fixlanka_static_content_is_privileged()) {
        return false;
    }

    return $mode === 'draft';
}

function fixlanka_static_content_get(PDO $pdo, string $contentType, bool $includeDraft = false): ?array
{
    $contentType = trim($contentType);
    if ($contentType === '') {
        return null;
    }

    if ($includeDraft) {
        $stmt = $pdo->prepare(
            "SELECT content_id, title, description, body, status, last_update, content_type
             FROM staticcontent
             WHERE content_type = :t
             ORDER BY content_id DESC
             LIMIT 1"
        );
        $stmt->execute([':t' => $contentType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    $stmt = $pdo->prepare(
        "SELECT content_id, title, description, body, status, last_update, content_type
         FROM staticcontent
         WHERE content_type = :t AND status = 'Published'
         ORDER BY content_id DESC
         LIMIT 1"
    );
    $stmt->execute([':t' => $contentType]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function fixlanka_static_content_for_page(PDO $pdo, string $contentType): ?array
{
    $includeDraft = fixlanka_static_content_include_draft_for_request();
    return fixlanka_static_content_get($pdo, $contentType, $includeDraft);
}

function fixlanka_static_content_body_to_html(string $body): string
{
    $body = (string)$body;

    // Hard block scripts to avoid accidental JS injection.
    $body = preg_replace('~<\s*script\b[^>]*>.*?<\s*/\s*script\s*>~is', '', $body) ?? '';

    // If the body already contains typical markup, render as HTML.
    $looksLikeHtml = (bool)preg_match(
        '/<\s*(p|div|h1|h2|h3|h4|h5|h6|ul|ol|li|br|strong|em|b|i|a|table|thead|tbody|tr|td|th|section|article|header|footer|nav)\b/i',
        $body
    );

    if ($looksLikeHtml) {
        return $body;
    }

    // Otherwise treat it as plain text, but upgrade simple patterns into
    // structured HTML so existing page styles apply.
    $lines = preg_split("/\r\n|\r|\n/", $body) ?: [];
    $hasAnyNonEmpty = false;
    foreach ($lines as $l) {
        if (trim((string)$l) !== '') {
            $hasAnyNonEmpty = true;
            break;
        }
    }

    if (!$hasAnyNonEmpty) {
        return '';
    }

    $out = '<div class="static-content-body">';

    $currentSectionOpen = false;
    $currentSectionHasTitle = false;
    $paraLines = [];
    $listItems = [];
    $listType = null; // 'ul' | 'ol' | null

    $flushParagraph = function () use (&$out, &$paraLines, &$currentSectionOpen) {
        if (empty($paraLines)) {
            return;
        }

        if (!$currentSectionOpen) {
            $out .= '<div class="terms-section static-content-section">';
            $currentSectionOpen = true;
        }

        $text = trim(implode(' ', array_map('trim', $paraLines)));
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $out .= '<p>' . $text . '</p>';
        $paraLines = [];
    };

    $flushList = function () use (&$out, &$listItems, &$listType, &$currentSectionOpen) {
        if (empty($listItems) || ($listType !== 'ul' && $listType !== 'ol')) {
            $listItems = [];
            $listType = null;
            return;
        }

        if (!$currentSectionOpen) {
            $out .= '<div class="terms-section static-content-section">';
            $currentSectionOpen = true;
        }

        $tag = $listType;
        $out .= '<' . $tag . '>';
        foreach ($listItems as $it) {
            $it = htmlspecialchars(trim((string)$it), ENT_QUOTES, 'UTF-8');
            if ($it === '') {
                continue;
            }
            $out .= '<li>' . $it . '</li>';
        }
        $out .= '</' . $tag . '>';

        $listItems = [];
        $listType = null;
    };

    $openNewSection = function (string $title) use (&$out, &$currentSectionOpen, &$currentSectionHasTitle) {
        if ($currentSectionOpen) {
            $out .= '</div>';
        }
        $currentSectionOpen = true;
        $currentSectionHasTitle = true;
        $safeTitle = htmlspecialchars(trim($title), ENT_QUOTES, 'UTF-8');
        $out .= '<div class="terms-section static-content-section">';
        $out .= '<h2 class="section-title">' . $safeTitle . '</h2>';
    };

    foreach ($lines as $rawLine) {
        $line = trim((string)$rawLine);

        if ($line === '') {
            $flushParagraph();
            $flushList();
            continue;
        }

        // Section heading: === OUR STORY ===
        if (preg_match('/^=+\s*(.+?)\s*=+$/', $line, $m)) {
            $flushParagraph();
            $flushList();
            $openNewSection((string)$m[1]);
            continue;
        }

        // Bullet list: - item / * item
        if (preg_match('/^[-*]\s+(.+)$/', $line, $m)) {
            $flushParagraph();
            if ($listType !== null && $listType !== 'ul') {
                $flushList();
            }
            $listType = 'ul';
            $listItems[] = (string)$m[1];
            continue;
        }

        // Ordered list: 1. item
        if (preg_match('/^\d+\.\s+(.+)$/', $line, $m)) {
            $flushParagraph();
            if ($listType !== null && $listType !== 'ol') {
                $flushList();
            }
            $listType = 'ol';
            $listItems[] = (string)$m[1];
            continue;
        }

        // Normal paragraph text.
        $flushList();
        $paraLines[] = $line;
    }

    $flushParagraph();
    $flushList();

    if ($currentSectionOpen) {
        $out .= '</div>';
    }

    $out .= '</div>';
    return $out;
}

function fixlanka_static_content_excerpt(string $body, int $limit = 140): string
{
    $body = (string)$body;

    // Hard block scripts to avoid accidental JS injection.
    $body = preg_replace('~<\s*script\b[^>]*>.*?<\s*/\s*script\s*>~is', '', $body) ?? '';

    // Convert HTML to text if needed.
    $text = strip_tags($body);
    $text = (string)$text;

    // Remove template markers commonly used in plain-text static content.
    // - === HEADING === lines
    $text = preg_replace('/^=+\s*.+?\s*=+$/m', ' ', $text) ?? $text;
    // - Inline === HEADING === markers (sometimes appear mid-line after trimming)
    $text = preg_replace('/=+\s*[^=]{2,}?\s*=+/', ' ', $text) ?? $text;
    // - Q: / A: prefixes (used in FAQ-like text)
    $text = preg_replace('/^\s*[QA]\s*:\s*/mi', '', $text) ?? $text;
    // - List markers: -, *, 1.
    $text = preg_replace('/^\s*(?:[-*]|\d+\.)\s+/m', '', $text) ?? $text;

    // Collapse whitespace.
    $text = preg_replace('/\s+/', ' ', trim($text)) ?? '';
    if ($text === '') {
        return '';
    }

    if ($limit <= 0) {
        return $text;
    }

    $out = mb_substr($text, 0, $limit);
    if (mb_strlen($text) > $limit) {
        $out .= '...';
    }
    return $out;
}

function fixlanka_static_content_faq_body_to_html(string $body): string
{
    $body = (string)$body;

    // Hard block scripts to avoid accidental JS injection.
    $body = preg_replace('~<\s*script\b[^>]*>.*?<\s*/\s*script\s*>~is', '', $body) ?? '';

    // If already looks like the FAQ HTML template, return it.
    if (preg_match('/class\s*=\s*(["\"])faq-item\1/i', $body)) {
        return $body;
    }

    // Parse plain text blocks like:
    // === FOR USERS ===
    // Q: ...
    // A: ...
    $lines = preg_split("/\r\n|\r|\n/", $body) ?: [];

    $groups = [];
    $currentGroupTitle = '';
    $items = [];
    $pendingQuestion = null;
    $pendingAnswerLines = [];

    $flushItem = function () use (&$items, &$pendingQuestion, &$pendingAnswerLines) {
        if ($pendingQuestion === null) {
            $pendingAnswerLines = [];
            return;
        }

        $answer = trim(implode("\n", $pendingAnswerLines));
        $items[] = [
            'q' => trim((string)$pendingQuestion),
            'a' => $answer,
        ];
        $pendingQuestion = null;
        $pendingAnswerLines = [];
    };

    $flushGroup = function () use (&$groups, &$currentGroupTitle, &$items) {
        if (empty($items)) {
            $currentGroupTitle = '';
            return;
        }
        $groups[] = [
            'title' => trim((string)$currentGroupTitle),
            'items' => $items,
        ];
        $currentGroupTitle = '';
        $items = [];
    };

    foreach ($lines as $rawLine) {
        $line = trim((string)$rawLine);
        if ($line === '') {
            // Keep blank lines inside an answer.
            if ($pendingQuestion !== null) {
                $pendingAnswerLines[] = '';
            }
            continue;
        }

        if (preg_match('/^=+\s*(.+?)\s*=+$/', $line, $m)) {
            $flushItem();
            $flushGroup();
            $currentGroupTitle = trim((string)$m[1]);
            continue;
        }

        if (preg_match('/^Q\s*:\s*(.+)$/i', $line, $m)) {
            $flushItem();
            $pendingQuestion = (string)$m[1];
            continue;
        }

        if (preg_match('/^A\s*:\s*(.+)$/i', $line, $m)) {
            if ($pendingQuestion !== null) {
                $pendingAnswerLines[] = (string)$m[1];
            }
            continue;
        }

        if ($pendingQuestion !== null) {
            $pendingAnswerLines[] = $line;
        }
    }

    $flushItem();
    $flushGroup();

    if (empty($groups)) {
        // No Q/A detected; fallback to generic renderer.
        return fixlanka_static_content_body_to_html($body);
    }

    $out = '';
    foreach ($groups as $group) {
        $title = (string)($group['title'] ?? '');
        if ($title !== '') {
            $out .= '<div class="faq-group-title" style="font-weight:700; margin: 18px 0 10px;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        foreach (($group['items'] ?? []) as $it) {
            $q = htmlspecialchars((string)($it['q'] ?? ''), ENT_QUOTES, 'UTF-8');
            $a = htmlspecialchars((string)($it['a'] ?? ''), ENT_QUOTES, 'UTF-8');
            $a = nl2br($a);

            $out .= '<div class="faq-item">';
            $out .= '  <button class="faq-question">';
            $out .= '    <span class="question-text">' . $q . '</span>';
            $out .= '    <i class="fas fa-chevron-down faq-icon"></i>';
            $out .= '  </button>';
            $out .= '  <div class="faq-answer"><p>' . $a . '</p></div>';
            $out .= '</div>';
        }
    }

    return $out;
}
?>
