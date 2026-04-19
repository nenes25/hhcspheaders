<?php

use PHPUnit\Framework\TestCase;

class CspViolationTest extends TestCase
{
    private array $minimalReport = [
        'document-uri' => 'https://example.com/page',
        'blocked-uri' => 'https://evil.com/script.js',
        'violated-directive' => 'script-src',
    ];

    public function testCreateFromReportReturnsViolationInstance(): void
    {
        $violation = CspViolation::createFromReport($this->minimalReport);
        $this->assertInstanceOf(CspViolation::class, $violation);
    }

    public function testCreateFromReportMapsRequiredFields(): void
    {
        $violation = CspViolation::createFromReport($this->minimalReport);

        $this->assertSame('https://example.com/page', $violation->document_uri);
        $this->assertSame('https://evil.com/script.js', $violation->blocked_uri);
        $this->assertSame('script-src', $violation->violated_directive);
    }

    public function testCreateFromReportMapsAllOptionalFields(): void
    {
        $report = array_merge($this->minimalReport, [
            'effective-directive' => 'script-src',
            'original-policy' => "script-src 'self'",
            'disposition' => 'enforce',
            'status-code' => 200,
        ]);

        $violation = CspViolation::createFromReport($report);

        $this->assertSame('script-src', $violation->effective_directive);
        $this->assertSame("script-src 'self'", $violation->original_policy);
        $this->assertSame('enforce', $violation->disposition);
        $this->assertSame(200, $violation->status_code);
    }

    public function testCreateFromReportMissingOptionalFieldsAreNull(): void
    {
        $violation = CspViolation::createFromReport($this->minimalReport);

        $this->assertNull($violation->effective_directive);
        $this->assertNull($violation->original_policy);
        $this->assertNull($violation->disposition);
        $this->assertNull($violation->status_code);
    }

    public function testCreateFromReportStatusCodeIsCastToInt(): void
    {
        $report = array_merge($this->minimalReport, ['status-code' => '404']);
        $violation = CspViolation::createFromReport($report);

        $this->assertSame(404, $violation->status_code);
        $this->assertIsInt($violation->status_code);
    }

    public function testCreateFromReportSetsOccurrencesToOne(): void
    {
        $violation = CspViolation::createFromReport($this->minimalReport);
        $this->assertSame(1, $violation->occurrences);
    }

    public function testCreateFromReportSetsIsResolvedToFalse(): void
    {
        $violation = CspViolation::createFromReport($this->minimalReport);
        $this->assertFalse($violation->is_resolved);
    }

    public function testCreateFromReportSetsTimestamps(): void
    {
        $before = date('Y-m-d H:i:s');
        $violation = CspViolation::createFromReport($this->minimalReport);
        $after = date('Y-m-d H:i:s');

        $this->assertGreaterThanOrEqual($before, $violation->first_seen);
        $this->assertLessThanOrEqual($after, $violation->first_seen);
        $this->assertSame($violation->first_seen, $violation->last_seen);
        $this->assertSame($violation->first_seen, $violation->date_add);
        $this->assertSame($violation->first_seen, $violation->date_upd);
    }

    public function testCreateFromReportMissingDocumentUriDefaultsToEmptyString(): void
    {
        $report = [
            'blocked-uri' => 'https://evil.com/script.js',
            'violated-directive' => 'script-src',
        ];
        $violation = CspViolation::createFromReport($report);

        $this->assertSame('', $violation->document_uri);
    }

    public function testIncrementOccurrenceIncrementsCounter(): void
    {
        $violation = new CspViolation();
        $violation->occurrences = 5;

        $violation->incrementOccurrence();

        $this->assertSame(6, $violation->occurrences);
    }

    public function testIncrementOccurrenceReturnsBool(): void
    {
        $violation = new CspViolation();
        $violation->occurrences = 1;

        $result = $violation->incrementOccurrence();

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testIncrementOccurrenceUpdatesLastSeen(): void
    {
        $violation = new CspViolation();
        $violation->occurrences = 1;
        $violation->last_seen = '2020-01-01 00:00:00';

        $before = date('Y-m-d H:i:s');
        $violation->incrementOccurrence();
        $after = date('Y-m-d H:i:s');

        $this->assertGreaterThanOrEqual($before, $violation->last_seen);
        $this->assertLessThanOrEqual($after, $violation->last_seen);
    }

    public function testIncrementOccurrenceUpdatesDateUpd(): void
    {
        $violation = new CspViolation();
        $violation->occurrences = 1;
        $violation->date_upd = '2020-01-01 00:00:00';

        $before = date('Y-m-d H:i:s');
        $violation->incrementOccurrence();
        $after = date('Y-m-d H:i:s');

        $this->assertGreaterThanOrEqual($before, $violation->date_upd);
        $this->assertLessThanOrEqual($after, $violation->date_upd);
    }

    public function testMarkAsResolvedSetsIsResolvedToTrue(): void
    {
        $violation = new CspViolation();
        $violation->is_resolved = false;

        $violation->markAsResolved();

        $this->assertTrue($violation->is_resolved);
    }

    public function testMarkAsResolvedUpdatesDateUpd(): void
    {
        $violation = new CspViolation();
        $violation->date_upd = '2020-01-01 00:00:00';

        $before = date('Y-m-d H:i:s');
        $violation->markAsResolved();
        $after = date('Y-m-d H:i:s');

        $this->assertGreaterThanOrEqual($before, $violation->date_upd);
        $this->assertLessThanOrEqual($after, $violation->date_upd);
    }

    public function testMarkAsResolvedReturnsTrue(): void
    {
        $violation = new CspViolation();

        $result = $violation->markAsResolved();

        $this->assertTrue($result);
    }

    public function testMarkAllAsResolvedReturnsBool(): void
    {
        $result = CspViolation::markAllAsResolved();

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }
}
