@php
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
@endphp
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>{{ $plan->creator->name ?? 'GAD System' }}</Author>
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
   <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#0F2D59"/>
  </Style>
  <Style ss:ID="SubTitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#334155"/>
  </Style>
  <Style ss:ID="MetaLabel">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0F2D59"/>
   <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="MetaVal">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#000000"/>
   <Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="SecHeader">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#1E3A5F" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="ColHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0F172A"/>
   <Interior ss:Color="#E2E8F0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataText">
   <Alignment ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10"/>
  </Style>
  <Style ss:ID="DataNum">
   <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TotalLabel">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0F2D59"/>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0F2D59"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0F2D59"/>
   <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="TotalNum">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0F2D59"/>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0F2D59"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0F2D59"/>
   <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Plan and Budget">
  <Table ss:DefaultColumnWidth="60" ss:DefaultRowHeight="16">
   <Column ss:Width="90"/>
   <Column ss:Width="160"/>
   <Column ss:Width="160"/>
   <Column ss:Width="160"/>
   <Column ss:Width="160"/>
   <Column ss:Width="160"/>
   <Column ss:Width="85"/>
   <Column ss:Width="85"/>
   <Column ss:Width="85"/>
   <Column ss:Width="95"/>

   <!-- Titles -->
   <Row ss:Height="22">
    <Cell ss:MergeAcross="9" ss:StyleID="MainTitle"><Data ss:Type="String">ANNUAL GENDER AND DEVELOPMENT (GAD) PLAN AND BUDGET</Data></Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="9" ss:StyleID="SubTitle"><Data ss:Type="String">Fiscal Year {{ $plan->year }} | City Government of Cagayan de Oro</Data></Cell>
   </Row>
   <Row ss:Height="6"/>

   <!-- Meta Summary -->
   <Row>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Office / Dept:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="MetaVal"><Data ss:Type="String">{{ $plan->department->department_name ?? 'N/A' }}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Total Office Budget:</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaVal"><Data ss:Type="String">Php {{ number_format($plan->total_office_budget, 2) }}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Status:</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaVal"><Data ss:Type="String">{{ $plan->status }}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Total GAD Budget:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="MetaVal"><Data ss:Type="String">Php {{ number_format($plan->total_gad_budget, 2) }} ({{ number_format($plan->gad_percentage, 2) }}%)</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Prepared By:</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaVal"><Data ss:Type="String">{{ $plan->creator->name ?? 'N/A' }}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Date Exported:</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaVal"><Data ss:Type="String">{{ now()->format('F j, Y g:i A') }}</Data></Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Section A Header -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="9" ss:StyleID="SecHeader"><Data ss:Type="String">A. ORGANIZATION-FOCUSED AND CLIENT-FOCUSED ACTIVITIES</Data></Cell>
   </Row>
   <Row ss:Height="26">
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Focus</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Gender Issue / GAD Mandate</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">GAD Objective</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Relevant LGU Program / Project</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">GAD Activity</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Performance Target / Indicator</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">MOOE (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">PS (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">CO (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Row Total (Php)</Data></Cell>
   </Row>

   @php
    $sumMoe = 0; $sumPs = 0; $sumCo = 0;
   @endphp
   @forelse ($plan->entries as $entry)
   @php
    $rowTotal = (float)$entry->moe + (float)$entry->ps + (float)$entry->co;
    $sumMoe += (float)$entry->moe;
    $sumPs += (float)$entry->ps;
    $sumCo += (float)$entry->co;
   @endphp
   <Row>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->focused }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->gender_issue }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->gad_objective }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->lgu_program }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->gad_activity }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $entry->performance_indicator }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$entry->moe }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$entry->ps }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$entry->co }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ $rowTotal }}</Data></Cell>
   </Row>
   @empty
   <Row>
    <Cell ss:MergeAcross="9" ss:StyleID="DataText"><Data ss:Type="String">No entries recorded.</Data></Cell>
   </Row>
   @endforelse

   <!-- Section A Subtotal -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="5" ss:StyleID="TotalLabel"><Data ss:Type="String">SUB-TOTAL (ORG &amp; CLIENT FOCUSED):</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumMoe }}</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumPs }}</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumCo }}</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumMoe + $sumPs + $sumCo }}</Data></Cell>
   </Row>
   <Row ss:Height="12"/>

   <!-- Section B Header -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="9" ss:StyleID="SecHeader"><Data ss:Type="String">B. ATTRIBUTED PROGRAMS (HGDG ASSESSMENT)</Data></Cell>
   </Row>
   <Row ss:Height="24">
    <Cell ss:MergeAcross="3" ss:StyleID="ColHeader"><Data ss:Type="String">Title of LGU Program / Project</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="ColHeader"><Data ss:Type="String">Checklist Used</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">HGDG Score</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Total Annual Budget (Php)</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">GAD Attributed Budget (Php)</Data></Cell>
   </Row>

   @php
    $sumAttrBudget = 0; $sumAnnual = 0;
   @endphp
   @forelse ($plan->attributedPrograms as $attr)
   @php
    $sumAnnual += (float)$attr->total_annual_budget;
    $sumAttrBudget += (float)$attr->gad_attributed_budget;
   @endphp
   <Row>
    <Cell ss:MergeAcross="3" ss:StyleID="DataText"><Data ss:Type="String">{{ $attr->title }}</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="DataText"><Data ss:Type="String">{{ $attr->checklist }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ rtrim(rtrim(number_format($attr->hgdg_score, 2), '0'), '.') }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$attr->total_annual_budget }}</Data></Cell>
    <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ (float)$attr->gad_attributed_budget }}</Data></Cell>
   </Row>
   @empty
   <Row>
    <Cell ss:MergeAcross="9" ss:StyleID="DataText"><Data ss:Type="String">No attributed programs recorded.</Data></Cell>
   </Row>
   @endforelse

   <!-- Section B Subtotal -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="7" ss:StyleID="TotalLabel"><Data ss:Type="String">SUB-TOTAL (ATTRIBUTED PROGRAMS):</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumAnnual }}</Data></Cell>
    <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumAttrBudget }}</Data></Cell>
   </Row>

   <!-- Grand Total -->
   <Row ss:Height="24">
    <Cell ss:MergeAcross="7" ss:StyleID="TotalLabel"><Data ss:Type="String">GRAND TOTAL GAD BUDGET (A + B):</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="TotalNum"><Data ss:Type="Number">{{ (float)$plan->total_gad_budget }}</Data></Cell>
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
