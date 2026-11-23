@php
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
echo '<' . '?mso-application progid="Excel.Sheet"?' . '>' . "\n";
$entries = $entries ?? $plan->entries ?? collect();
$attributed = $attributed ?? $plan->attributedPrograms ?? collect();
$deptName = $plan->department->department_name ?? 'GAD Office';
@endphp
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>{{ $plan->creator->name ?? 'GAD System' }}</Author>
  <Created>{{ now()->toIso8601String() }}</Created>
  <Company>{{ $deptName }}</Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Top"/>
   <Borders/>
   <Font ss:FontName="Arial" ss:Size="10" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>

  {{-- Row 1 Title Bar: Black Background, Bold White Font, Left-Aligned --}}
  <Style ss:ID="TitleBar">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#000000" ss:Pattern="Solid"/>
  </Style>

  {{-- Header: Yellow Background (#FFFF00), Bold Dark Font (#000000) --}}
  <Style ss:ID="HdrYellow">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9" ss:Bold="1" ss:Color="#000000"/>
   <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
  </Style>

  {{-- Header: Purple Background (#7030A0), Bold White Font (#FFFFFF) --}}
  <Style ss:ID="HdrPurple">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#7030A0" ss:Pattern="Solid"/>
  </Style>

  {{-- Data Cells: Standard bordered, text wrap --}}
  <Style ss:ID="DataText">
   <Alignment ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9"/>
  </Style>

  <Style ss:ID="DataCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9"/>
  </Style>

  <Style ss:ID="DataNum">
   <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>

  {{-- Placeholder / START HERE row --}}
  <Style ss:ID="Placeholder">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9" ss:Italic="1" ss:Color="#555555"/>
  </Style>

  {{-- Totals Row --}}
  <Style ss:ID="TotalLabel">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9" ss:Bold="1" ss:Color="#000000"/>
   <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
  </Style>

  <Style ss:ID="TotalNum">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9" ss:Bold="1" ss:Color="#000000"/>
   <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
 </Styles>

 {{-- WORKSHEET 1: GAD Plan and Budget --}}
 <Worksheet ss:Name="GAD Plan and Budget">
  <Table ss:DefaultColumnWidth="65" ss:DefaultRowHeight="18">
   {{-- 17 Columns (A to Q) matching government template --}}
   <Column ss:Index="1" ss:Width="110"/>  {{-- Col A: Client / Org Focused --}}
   <Column ss:Index="2" ss:Width="110"/>  {{-- Col B: Gender Issue / GAD Mandate --}}
   <Column ss:Index="3" ss:Width="170"/>  {{-- Col C: Title / Description --}}
   <Column ss:Index="4" ss:Width="130"/>  {{-- Col D: Supporting Statistics --}}
   <Column ss:Index="5" ss:Width="100"/>  {{-- Col E: Source --}}
   <Column ss:Index="6" ss:Width="140"/>  {{-- Col F: PPA Sectors --}}
   <Column ss:Index="7" ss:Width="150"/>  {{-- Col G: GAD Objective --}}
   <Column ss:Index="8" ss:Width="150"/>  {{-- Col H: Relevant LGU Program --}}
   <Column ss:Index="9" ss:Width="160"/>  {{-- Col I: Activity Category --}}
   <Column ss:Index="10" ss:Width="160"/> {{-- Col J: GAD Activity --}}
   <Column ss:Index="11" ss:Width="160"/> {{-- Col K: Performance Indicator --}}
   <Column ss:Index="12" ss:Width="110"/> {{-- Col L: Target Date START --}}
   <Column ss:Index="13" ss:Width="110"/> {{-- Col M: Target Date END --}}
   <Column ss:Index="14" ss:Width="85"/>  {{-- Col N: MOOE --}}
   <Column ss:Index="15" ss:Width="85"/>  {{-- Col O: PS --}}
   <Column ss:Index="16" ss:Width="85"/>  {{-- Col P: CO --}}
   <Column ss:Index="17" ss:Width="150"/> {{-- Col Q: Lead / Responsible Office --}}

   {{-- Row 1: Black Title Bar --}}
   <Row ss:Height="24">
    <Cell ss:MergeAcross="16" ss:StyleID="TitleBar"><Data ss:Type="String"> GAD Plan and Budget</Data></Cell>
   </Row>

   {{-- Row 2: Main Column Headers (Multi-line with MergeDown for cols A-M and Q) --}}
   <Row ss:Height="48">
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">1. Client-Focused&#10;2. Organization-Focused&#10;(Choose 1)</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">1 = Gender Issue&#10;2 = GAD Mandate&#10;(Choose 1)</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrPurple"><Data ss:Type="String">Gender Issue or&#10;GAD Mandate&#10;(Title / Description)</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">Supporting&#10;Statistics Data</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">Source</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">[Tag at least one(1) PPA Sectors]&#10;1 = Economic&#10;2 = Social&#10;3 = Infrastructure&#10;4 = Environment&#10;5 = Institutional</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrPurple"><Data ss:Type="String">GAD Objective</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrPurple"><Data ss:Type="String">Relevant LGU&#10;Program or Project</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">Activity Category&#10;[Tag at least one (1) Activity category and&#10;See full details on ACTIVITY CATEGORIES SHEET]</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrPurple"><Data ss:Type="String">GAD Activity</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrPurple"><Data ss:Type="String">Performance Indicator&#10;and Target</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">Target Date of Implementation&#10;(START)&#10;(YYYY-MM-DD)</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HdrYellow"><Data ss:Type="String">Target Date of Implementation&#10;(END)&#10;(YYYY-MM-DD)</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="HdrPurple"><Data ss:Type="String">GAD Budget</Data></Cell>
    <Cell ss:Index="17" ss:MergeDown="1" ss:StyleID="HdrPurple"><Data ss:Type="String">Lead or&#10;Responsible Office</Data></Cell>
   </Row>

   {{-- Row 3: Sub-headers for GAD Budget (MOOE, PS, CO) --}}
   <Row ss:Height="22">
    <Cell ss:Index="14" ss:StyleID="HdrPurple"><Data ss:Type="String">MOOE</Data></Cell>
    <Cell ss:Index="15" ss:StyleID="HdrPurple"><Data ss:Type="String">PS</Data></Cell>
    <Cell ss:Index="16" ss:StyleID="HdrPurple"><Data ss:Type="String">CO</Data></Cell>
   </Row>

   {{-- Data Rows --}}
   @if($entries->count() > 0)
    @foreach ($entries as $e)
    <Row ss:Height="45">
     <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $e->focused }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->gender_issue }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->title_description }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->supporting_statistics }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->statistics_source }}</Data></Cell>
     <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $e->ppa_sector }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->gad_objective }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->lgu_program }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->activity_category }}{{ $e->other_activity_category ? ' - ' . $e->other_activity_category : '' }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->gad_activity }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $e->performance_indicator }}</Data></Cell>
     <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $e->date_of_implementation ? \Carbon\Carbon::parse($e->date_of_implementation)->format('Y-m-d') : '' }}</Data></Cell>
     <Cell ss:StyleID="DataCenter"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ $e->moe ?? 0 }}</Data></Cell>
     <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ $e->ps ?? 0 }}</Data></Cell>
     <Cell ss:StyleID="DataNum"><Data ss:Type="Number">{{ $e->co ?? 0 }}</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $deptName }}</Data></Cell>
    </Row>
    @endforeach

    {{-- Total Row --}}
    <Row ss:Height="24">
     <Cell ss:MergeAcross="12" ss:StyleID="TotalLabel"><Data ss:Type="String">SUBTOTAL (A. Client &amp; Org-Focused) </Data></Cell>
     <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $entries->sum('moe') }}</Data></Cell>
     <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $entries->sum('ps') }}</Data></Cell>
     <Cell ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $entries->sum('co') }}</Data></Cell>
     <Cell ss:StyleID="TotalLabel"><Data ss:Type="String"></Data></Cell>
    </Row>
   @else
    {{-- Empty template row showing START HERE matching template --}}
    <Row ss:Height="26">
     <Cell ss:StyleID="Placeholder"><Data ss:Type="String">START HERE</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String"></Data></Cell>
     <Cell ss:StyleID="DataNum"><Data ss:Type="Number">0.00</Data></Cell>
     <Cell ss:StyleID="DataNum"><Data ss:Type="Number">0.00</Data></Cell>
     <Cell ss:StyleID="DataNum"><Data ss:Type="Number">0.00</Data></Cell>
     <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $deptName }}</Data></Cell>
    </Row>
   @endif

   {{-- Attributed Programs Section (if any programs attached) --}}
   @if($attributed->count() > 0)
    <Row ss:Height="15"/>
    <Row ss:Height="24">
     <Cell ss:MergeAcross="16" ss:StyleID="TitleBar"><Data ss:Type="String"> B. ATTRIBUTED PROGRAMS (HGDG ASSESSMENT)</Data></Cell>
    </Row>
    <Row ss:Height="36">
     <Cell ss:MergeAcross="5" ss:StyleID="HdrPurple"><Data ss:Type="String">Title of LGU Program or Project</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="HdrPurple"><Data ss:Type="String">HGDG Checklist Used</Data></Cell>
     <Cell ss:MergeAcross="1" ss:StyleID="HdrPurple"><Data ss:Type="String">HGDG Score</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="HdrPurple"><Data ss:Type="String">Total Annual Program Budget</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="HdrPurple"><Data ss:Type="String">GAD Attributed Budget</Data></Cell>
    </Row>

    @foreach($attributed as $a)
    <Row ss:Height="30">
     <Cell ss:MergeAcross="5" ss:StyleID="DataText"><Data ss:Type="String">{{ $a->title }}</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="DataText"><Data ss:Type="String">{{ $a->checklist }}</Data></Cell>
     <Cell ss:MergeAcross="1" ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $a->hgdg_score }}</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="DataNum"><Data ss:Type="Number">{{ $a->total_annual_budget ?? 0 }}</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="DataNum"><Data ss:Type="Number">{{ $a->gad_attributed_budget ?? 0 }}</Data></Cell>
    </Row>
    @endforeach

    @php
    $sumAttrBudget = $attributed->sum(fn($p) => (float)($p->total_annual_budget ?? 0));
    $sumAttrGad = $attributed->sum(fn($p) => (float)($p->gad_attributed_budget ?? 0));
    @endphp
    <Row ss:Height="24">
     <Cell ss:MergeAcross="10" ss:StyleID="TotalLabel"><Data ss:Type="String">SUBTOTAL (B. Attributed Programs) </Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumAttrBudget }}</Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $sumAttrGad }}</Data></Cell>
    </Row>

    {{-- GRAND TOTAL ROW --}}
    @php
    $entriesTotal = $entries->sum('moe') + $entries->sum('ps') + $entries->sum('co');
    $grandTotal = $entriesTotal + $sumAttrGad;
    @endphp
    <Row ss:Height="26">
     <Cell ss:MergeAcross="13" ss:StyleID="TotalLabel"><Data ss:Type="String">GRAND TOTAL GAD BUDGET (A + B) </Data></Cell>
     <Cell ss:MergeAcross="2" ss:StyleID="TotalNum"><Data ss:Type="Number">{{ $grandTotal }}</Data></Cell>
    </Row>
   @endif
  </Table>
 </Worksheet>

 {{-- WORKSHEET 2: ACTIVITY CATEGORIES (Referenced in Col I header) --}}
 <Worksheet ss:Name="ACTIVITY CATEGORIES">
  <Table ss:DefaultColumnWidth="80" ss:DefaultRowHeight="20">
   <Column ss:Index="1" ss:Width="60"/>
   <Column ss:Index="2" ss:Width="220"/>
   <Column ss:Index="3" ss:Width="420"/>

   <Row ss:Height="24">
    <Cell ss:MergeAcross="2" ss:StyleID="TitleBar"><Data ss:Type="String"> GAD ACTIVITY CATEGORIES REFERENCE SHEET</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="HdrPurple"><Data ss:Type="String">Code</Data></Cell>
    <Cell ss:StyleID="HdrPurple"><Data ss:Type="String">Activity Category</Data></Cell>
    <Cell ss:StyleID="HdrPurple"><Data ss:Type="String">Description / Coverage</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">1</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Capacity Development</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Trainings, seminars, workshops, orientations on GAD concepts, GST, gender analysis tools, etc.</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">2</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Advocacy</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Campaigns, IEC materials production, Women's Month / 18-day campaign activities, forums, summits.</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">3</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Policy Formulation</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Formulation, enhancement, or amendment of local ordinances, resolutions, codes, executive orders with GAD perspective.</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">4</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Research and Data Generation</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Establishment or maintenance of sex-disaggregated database (SDD), gender studies, surveys, and profiling.</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">5</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Gender Mainstreaming Mechanism</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Institutional mechanisms: GFPS meetings, GAD focal point strengthening, monitoring and evaluation system setup.</Data></Cell>
   </Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">6</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Others</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">Other GAD activities not covered by the 5 specific categories above.</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>