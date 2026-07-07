- [ ] Investigate why "Generate Report" button (API `/reports/sales`) still not working while PDF export works.
- [ ] Add logic so the PDF view shows conditional message:
  - If report has orders => show “Report downloaded” (or similar) inside PDF.
  - If no orders => show “No report data / report not generated” inside PDF.
- [ ] Update frontend so Generate Report shows conditional UI message when no report results.
- [ ] Validate PDF conditional message by checking `resources/views/report/sales-pdf.blade.php` data usage (`$orders`, `$summary`).

