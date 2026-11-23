@php
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
echo '<' . '?mso-application progid="Excel.Sheet"?' . '>' . "\n";
$entries = $entries ?? $report->entries ?? collect();
$attributed = $attributed ?? $report->attributedPrograms ?? collect();
@endphp
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>{{ $report->creator->name ?? 'GAD System' }}</Author>
  <Created>{{ now()->toIso8601String() }}</Created>
  <Company>City Government of Cagayan de Oro</Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Top"/>
   <Borders/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="MainTitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#831843"/>
  </Style>
  <Style ss:ID="SubTitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#334155"/>
  </Style>
  <Style ss:ID="MetaLabel">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#831843"/>
   <Interior ss:Color="#FDF2F8" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="MetaVal">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#000000"/>
   <Interior ss:Color="#FAFAFA" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="SecHeader">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#831843" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="ColHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F472B6"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F472B6"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F472B6"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F472B6"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#831843"/>
   <Interior ss:Color="#FCE7F3" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataText">
   <Alignment ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10"/>
  </Style>
  <Style ss:ID="DataNum">
   <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TotalLabel">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#831843"/>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#831843"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#831843"/>
   <Interior ss:Color="#FDF2F8" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="TotalNum">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#831843"/>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#831843"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#831843"/>
   <Interior ss:Color="#FDF2F8" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Accomplishment Report">
  <Table ss:DefaultColumnWidth="60" ss:DefaultRowHeight="16">
   <Column ss:Width="150"/>
   <Column ss:Width="150"/>
   <Column ss:Width="150"/>
   <Column ss:Width="150"/>
   <Column ss:Width="150"/>
   <Column ss:Width="150"/>
   <Column ss:Width="110"/>
   <Column ss:Width="110"/>
   <Column ss:Width="140"/>

   <!-- Titles -->
   <Row ss:Height="22">
    <Cell ss:MergeAcross="8" ss:StyleID="MainTitle"><Data ss:Type="String">ANNUAL GENDER AND DEVELOPMENT (GAD) ACCOMPLISHMENT REPORT</Data></Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="8" ss:StyleID="SubTitle"><Data ss:Type="String">Fiscal Year {{ $report->year }} | City Government of Cagayan de Oro</Data></Cell>
   </Row>
   <Row ss:Height="6"/>

   <!-- Meta Summary -->
   <Row>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Office / Dept:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="MetaVal"><Data ss:Type="String">{{ $report->department->department_name ?? 'N/A' }}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Total Office Budget:</Data></Cell>
    <Cell ss:StyleID="MetaVal"><Data ss:Type="String">Php {{ number_format($report->total_office_budget, 2) }}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Status:</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaVal"><Data ss:Type="String">{{ $report->status }}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Total GAD Expenditure:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="MetaVal"><Data ss:Type="String">Php {{ number_format($report->total_gad_expenditure, 2) }} ({{ number_format($report->gad_percentage, 2) }}%)</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Prepared By:</Data></Cell>
    <Cell ss:StyleID="MetaVal"><Data ss:Type="String">{{ $report->creator->name ?? 'N/A' }}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Date Exported:</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaVal"><Data ss:Type="String">{{ now()->format('F j, Y g:i A') }}</Data></Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Section A Header -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="8" ss:StyleID="SecHeader"><Data ss:Type="String">A. ORGANIZATION-FOCUSED AND CLIENT-FOCUSED ACCOMPLISHMENTS</Data></Cell>
   </Row>
   <Row ss:Height="26">
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Gender Issue / GAD Mandate</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">GAD Objective</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Relevant LGU Program / Project</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">GAD Activity</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Performance Target &amp; Indicator</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Actual Results</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Approved GAD Budget (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Actual Cost / Exp (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Variance / Remarks</Data></Cell>
   </Row>

   @php
    $sumApproved = 0; $sumActual = 0;
   @endphp
   @forelse ($report->entries as $entry)
   @php
    $sumApproved += (float)$entry->approved_gad_budget;
    $sumActual += (float)$entry->actual_cost;
   @endphp
   <Row>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->gender_issue }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->gad_objective }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->lgu_program }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->gad_activity }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->performance_indicator }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->actual_results }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$entry->approved_gad_budget }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$entry->actual_cost }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->variance_remarks }}</Data></Cell>
   </Row>
   @empty
   <Row>
    <Cell ss:MergeAcross="8" ss:StyleID="DataText"><Data ss:Type="String">No accomplishments recorded.</Data></Cell>
   </Row>
   @endforelse

   <!-- Section A Subtotal -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="5" ss:StyleID="TotalLabel"><Data ss:Type="String">SUB-TOTAL (ORG &amp; CLIENT FOCUSED):</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumApproved }}</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumActual }}</Data></Cell>
    <Cell ss:StyleID="TotalLabel"/>
   </Row>
   <Row ss:Height="12"/>

   <!-- Section B Header -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="8" ss:StyleID="SecHeader"><Data ss:Type="String">B. ATTRIBUTED PROGRAMS (HGDG ASSESSMENT)</Data></Cell>
   </Row>
   <Row ss:Height="24">
    <Cell ss:MergeAcross="2" ss:StyleID="ColHeader"><Data ss:Type="String">Title of LGU Program / Project</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="ColHeader"><Data ss:Type="String">HGDG PIMME/FIMME Score</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Total Annual Program Cost (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">GAD Attributed Cost (Php)</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="ColHeader"><Data ss:Type="String">Variance / Remarks</Data></Cell>
   </Row>

   @php
    $sumTotalCost = 0; $sumAttrCost = 0;
   @endphp
   @forelse ($report->attributedPrograms as $attr)
   @php
    $sumTotalCost += (float)$attr->total_annual_cost;
    $sumAttrCost += (float)$attr->gad_attributed_cost;
   @endphp
   <Row>
    <Cell ss:MergeAcross="2" ss:StyleID="DataText"><Data ss:Type="String">{{ $attr->title }}</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="DataText"><Data ss:Type="String">{{ $attr->checklist_score_desc }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$attr->total_annual_cost }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$attr->gad_attributed_cost }}</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="DataText"><Data ss:Type="String">{{ $attr->variance_remarks }}</Data></Cell>
   </Row>
   @empty
   <Row>
    <Cell ss:MergeAcross="8" ss:StyleID="DataText"><Data ss:Type="String">No attributed programs recorded.</Data></Cell>
   </Row>
   @endforelse

   <!-- Section B Subtotal -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="4" ss:StyleID="TotalLabel"><Data ss:Type="String">SUB-TOTAL (ATTRIBUTED PROGRAMS):</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumTotalCost }}</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumAttrCost }}</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="TotalLabel"/>
   </Row>

   <!-- Grand Total -->
   <Row ss:Height="24">
    <Cell ss:MergeAcross="5" ss:StyleID="TotalLabel"><Data ss:Type="String">GRAND TOTAL GAD EXPENDITURE (A + B):</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="TotalNum"><Data ss:Type="Number">{{ (float)$report->total_gad_expenditure }}</Data></Cell>
    <Cell ss:StyleID="TotalLabel"/>
   </Row>

  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Layout x:Orientation="Landscape"/>
   </PageSetup>
   <FitToPage/>
   <Print>
    <FitWidth>1</FitWidth>
    <FitHeight>0</FitHeight>
    <ValidPrinterInfo/>
   </Print>
   <Selected/>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
