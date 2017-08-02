
<?php 

$gliffyPath = '../src'; 
set_include_path( get_include_path() . PATH_SEPARATOR . $gliffyPath); 
require_once("Gliffy.php");


class DiagramsTest extends GliffyTest { 

private $BENDERS_CLOSET;
private $BENDERS_NEW_CLOSET_UNICODE;

    function __construct() {
        
        $this->BENDERS_CLOSET = "<stage keygen_seq=\"26\">" . 
            "<pageObj drawingHeight=\"570\" drawingWidth=\"1343\" istt=\"true\" stg=\"1\" pb=\"0\" gr=\"1\" fill=\"16777215\" height=\"5000\" width=\"5000\">" . 
            "<objects>" . 
            "<object order=\"0\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"Wall_V2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"15\" width=\"985\" y=\"502.5\" x=\"783\" shp_id=\"11\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"1\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"Wall_V2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"15\" width=\"985\" y=\"178\" x=\"783\" shp_id=\"12\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"2\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"VertWall\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"325\" width=\"15\" y=\"337.5\" x=\"362.4\" shp_id=\"13\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"3\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"VertWall\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"325\" width=\"15\" y=\"338\" x=\"298\" shp_id=\"14\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"4\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"16777215\" svg_id=\"Door2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"100\" width=\"100\" y=\"445\" x=\"249.9\" shp_id=\"15\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"5\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"16777215\" svg_id=\"SingleWindow_V2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"-90.2893701634406\" height=\"15\" width=\"320\" y=\"338.001530426798\" x=\"1274.39394712337\" shp_id=\"16\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"6\" connPtPattern=\"none\" symbol_id=\"Fireplace\" sublibraryid=\"structure\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0x9A9A9A\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"92.8\" width=\"160\" y=\"219.8\" x=\"824.9\" shp_id=\"17\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"7\" connPtPattern=\"none\" symbol_id=\"QueenBed\" sublibraryid=\"bedroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xFFFF66\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"179.8575536508\" height=\"160\" width=\"124.8\" y=\"415\" x=\"832\" shp_id=\"18\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"8\" connPtPattern=\"none\" symbol_id=\"Armoire\" sublibraryid=\"bedroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xD59758\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"70.4\" width=\"160\" y=\"211.4\" x=\"559.9\" shp_id=\"19\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"9\" connPtPattern=\"none\" symbol_id=\"Lamp\" sublibraryid=\"bedroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xDBD0A2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"48\" width=\"80\" y=\"467\" x=\"719.9\" shp_id=\"20\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"10\" connPtPattern=\"none\" symbol_id=\"Toilet\" sublibraryid=\"bathroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xE4BC96\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"80\" width=\"48\" y=\"220\" x=\"941.9\" shp_id=\"21\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"11\" connPtPattern=\"none\" symbol_id=\"Shower\" sublibraryid=\"bathroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xFFE5AB\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"80\" width=\"80\" y=\"455\" x=\"429.9\" shp_id=\"22\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"12\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"16777215\" svg_id=\"Door1\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"100\" width=\"100\" y=\"225\" x=\"404.9\" shp_id=\"23\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"13\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"90.5509039792186\" height=\"28\" width=\"150\" y=\"319\" x=\"322\" shp_id=\"24\" class=\"text_shape\">" . 
            "<text>" . 
            "<![CDATA[<P ALIGN=\"CENTER\">" . 
            "<FONT FACE=\"Arial\" SIZE=\"18\" COLOR=\"#000000\" LETTERSPACING=\"0\" KERNING=\"0\">" . 
            "Bender&apos;s Room</FONT>" . 
            "</P>" . 
            "]]>" . 
            "</text>" . 
            "</object>" . 
            "<object order=\"14\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"28\" width=\"150\" y=\"329\" x=\"635\" shp_id=\"25\" class=\"text_shape\">" . 
            "<text>" . 
            "<![CDATA[<P ALIGN=\"CENTER\">" . 
            "<FONT FACE=\"Arial\" SIZE=\"18\" COLOR=\"#000000\" LETTERSPACING=\"0\" KERNING=\"0\">" . 
            "Fry&apos;s Room</FONT>" . 
            "</P>" . 
            "]]>" . 
            "</text>" . 
            "</object>" . 
            "</objects>" . 
            "</pageObj>" . 
            "</stage>" . 
            "";

        $BENDERS_NEW_CLOSET_UNICODE = "<stage keygen_seq=\"26\">" . 
            "<pageObj drawingHeight=\"570\" drawingWidth=\"1243\" istt=\"true\" stg=\"1\" pb=\"0\" gr=\"1\" fill=\"16777215\" height=\"5000\" width=\"5000\">" . 
            "<objects>" . 
            "<object order=\"0\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"Wall_V2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"15\" width=\"985\" y=\"502.5\" x=\"783\" shp_id=\"11\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"1\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"Wall_V2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"15\" width=\"985\" y=\"178\" x=\"783\" shp_id=\"12\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"2\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"VertWall\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"325\" width=\"15\" y=\"337.5\" x=\"362.4\" shp_id=\"13\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"3\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"0\" svg_id=\"VertWall\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"325\" width=\"15\" y=\"338\" x=\"298\" shp_id=\"14\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"4\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"16777215\" svg_id=\"Door2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"100\" width=\"100\" y=\"445\" x=\"249.9\" shp_id=\"15\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"5\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"16777215\" svg_id=\"SingleWindow_V2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"-90.2893701634406\" height=\"15\" width=\"320\" y=\"338.001530426798\" x=\"1274.39394712337\" shp_id=\"16\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"6\" connPtPattern=\"none\" symbol_id=\"Fireplace\" sublibraryid=\"structure\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0x9A9A9A\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"92.8\" width=\"160\" y=\"219.8\" x=\"824.9\" shp_id=\"17\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"7\" connPtPattern=\"none\" symbol_id=\"QueenBed\" sublibraryid=\"bedroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xFFFF66\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"179.8575536508\" height=\"160\" width=\"124.8\" y=\"415\" x=\"832\" shp_id=\"18\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"8\" connPtPattern=\"none\" symbol_id=\"Armoire\" sublibraryid=\"bedroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xD59758\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"70.4\" width=\"160\" y=\"211.4\" x=\"559.9\" shp_id=\"19\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"9\" connPtPattern=\"none\" symbol_id=\"Lamp\" sublibraryid=\"bedroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xDBD0A2\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"48\" width=\"80\" y=\"467\" x=\"719.9\" shp_id=\"20\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"10\" connPtPattern=\"none\" symbol_id=\"Toilet\" sublibraryid=\"bathroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xE4BC96\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"80\" width=\"48\" y=\"220\" x=\"941.9\" shp_id=\"21\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"11\" connPtPattern=\"none\" symbol_id=\"Shower\" sublibraryid=\"bathroom\" libraryid=\"com.gliffy.floorplan\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"none\" fill=\"0xFFE5AB\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"true\" rot=\"0\" height=\"80\" width=\"80\" y=\"455\" x=\"429.9\" shp_id=\"22\" class=\"GliffyFlashShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"12\" dshad=\"false\" gradon=\"false\" linew=\"1\" linec=\"0\" fill=\"16777215\" svg_id=\"Door1\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"100\" width=\"100\" y=\"225\" x=\"404.9\" shp_id=\"23\" class=\"GliffySVGShape\">" . 
            "<text/>" . 
            "<connlines/>" . 
            "</object>" . 
            "<object order=\"13\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"90.5509039792186\" height=\"28\" width=\"150\" y=\"319\" x=\"322\" shp_id=\"24\" class=\"text_shape\">" . 
            "<text>" . 
            "<![CDATA[<P ALIGN=\"CENTER\">" . 
            "<FONT FACE=\"Arial\" SIZE=\"18\" COLOR=\"#FF0000\" LETTERSPACING=\"0\" KERNING=\"0\">" . 
            "Bender&apos;s Room\u221a\u00df - \u221a\u2265 - \u221a\u00a5 - \u221a\u00b0 - \u201e\u00c5\u00e8\u2030\u03a9\u00fa\u201e\u00c7\u00e4\u00c1\u00f5\u00a5 - \u00ce\u221e\u00ef\u00cf\u221e\u03a9\u00ce\u00d8\u00ba - \u2013\u00e6 \u2013\u222b \u2013\u00a5\u2013\u00b5\u2013\u03c0\u2014\u00c5\u2014\u00c7\u2013\u2264\u2013\u220f\u2014\u00e9\u00ac\u00aa. - Svenska spr\u221a\u2022ket - \u0178\u00d6\u00ff\u03c0 \u00ff\u00df\u0178\u00d1\u00ff\u00a8\u0178\u00dc\u00ff\u00df\u00ff\u00b6\u0178\u00e4\u00ff\u00a9 \u00ff\u00df\u0178\u00d1\u00ff\u00d8\u0178\u00e0\u0178\u00d1\u0178\u00e4\u00ff\u00a9</FONT>" . 
            "</P>" . 
            "]]>" . 
            "</text>" . 
            "</object>" . 
            "<object order=\"14\" text-horizontal-pos=\"center\" text-vertical-pos=\"middle\" lock=\"false\" fixed-aspect=\"false\" rot=\"0\" height=\"28\" width=\"150\" y=\"329\" x=\"635\" shp_id=\"25\" class=\"text_shape\">" . 
            "<text>" . 
            "<![CDATA[<P ALIGN=\"CENTER\">" . 
            "<FONT FACE=\"Arial\" SIZE=\"18\" COLOR=\"#FF0000\" LETTERSPACING=\"0\" KERNING=\"0\">" . 
            "Fry&apos;s Room</FONT>" . 
            "</P>" . 
            "]]>" . 
            "</text>" . 
            "</object>" . 
            "</objects>" . 
            "</pageObj>" . 
            "</stage>" . 
            "";
         

   
    }


    function testCode() {
        $gliffyAdminUsername = "testuser@gliffy.com"; 
        $gliffy = new Gliffy($gliffyAdminUsername); 
        $account = $gliffy->getAccountInfo(); 
   
        $firstName ="My First Diagram";
        $secondName = "My Second Diagram";
        $thirdName = "My Third Diagram";

        $firstId = $gliffy->createDiagram($firstName);
        $secondId = $gliffy->createDiagram($secondName);
        $thirdId = $gliffy->createDiagram($thirdName);

        //verify that the diagram id returned is an integer
        $this->assertTrue( is_int( $firstId ) );
        $this->assertTrue( is_int( $secondId ) );
        $this->assertTrue( is_int( $thirdId ) );

        $diagramsList = $gliffy->getDiagrams('ROOT');  
        $this->assertTrue( is_array( $diagramsList ) );

        //make sure we only got 3 diagrams
        $this->assertTrue( sizeof( $diagramsList ) == 3 ); 

        //delete the third diagram
        $gliffy->deleteDiagram( $thirdId );

        //re-fetch the diagram list
        $diagramsList = $gliffy->getDiagrams('ROOT');  
        $this->assertTrue( is_array( $diagramsList ) );

        //make sure we only got 2 diagrams
        $this->assertTrue( sizeof( $diagramsList ) == 2 ); 

        //make sure the diagram names/ids are correct 
        if( $diagramsList[0]->id == $firstId ) { 
            $firstDiagram = $diagramsList[0];  
            $secondDiagram = $diagramsList[1];  
        } else {
            $firstDiagram = $diagramsList[1];  
            $secondDiagram = $diagramsList[0];  
        } 

        $this->assertStringsEqual( $firstDiagram->name, $firstName);
        $this->assertStringsEqual( $secondDiagram->name, $secondName );
        
        //test the meta data calls, verify we get back what we expect
        $firstMetaData = $gliffy->getDiagramMetaData($firstId);  
        $this->assertStringsEqual( $firstMetaData->name, $firstName );
        $this->assertTrue( $firstMetaData->id == $firstId );    
        
        $secondMetaData = $gliffy->getDiagramMetaData($secondId);  
        $this->assertStringsEqual( $secondMetaData->name, $secondName );
        $this->assertTrue( $secondMetaData->id ==  $secondId );    

        //make sure we get null for non-existant diagram
        $expectedNullMetaData = $gliffy->getDiagramMetaData(12345);  
        $this->assertTrue( is_null( $expectedNullMetaData ) );

        $firstReturnURL = "http://www.silvertie.com";
        $firstReturnText = "Back to SilverTie.com";
        $firstEditDiagramLink = $gliffy->getEditDiagramLink($firstId,$firstReturnURL,$firstReturnText); 

        $this->assertTrue( is_string( $firstEditDiagramLink  )  );      

        //Check to make sure the URL actually resolves to something
        $ch = curl_init( $firstEditDiagramLink );   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);       
       
      
        //make sure output exists
        $this->assertTrue( !is_null( $output ) ); 
        //make sure we get a 200 status code 
        $this->assertGoodStatusCode( $ch ); 

        curl_close($ch);

        //test that we can update diagram content as XML, and then get it back
        $gliffy->updateDiagramContent($firstId,$this->BENDERS_CLOSET); 
        $gottenContentOne =  $this->stripXMLHeader( $gliffy->getDiagramAsXML($firstId) ); 
        $this->assertStringsEqual( $this->BENDERS_CLOSET,$gottenContentOne);

        //test unicode chars 
        //NOTE: These tests are commented out until unicode_encode() works with a common version of php
        //$gliffy->updateDiagramContent($firstId, unicode_encode(  $this->BENDERS_NEW_CLOSET_UNICODE,'ISO-8859-2' ) ); 
        //$gottenContentOne =  $this->stripXMLHeader( $gliffy->getDiagramAsXML($firstId) ); 
        //$this->assertStringsEqual( unicode_encode( $this->BENDERS_NEW_CLOSET_UNICODE,'ISO-8859-2') ,$gottenContentOne);

        //test getting a diagram as a PNG, writing to a file 
        $fileOnePNG = tempnam( sys_get_temp_dir() , "PHPTEST-") . ".png" ;
        $gliffy->getDiagramAsImage($firstId,Gliffy::MIME_TYPE_PNG,$fileOnePNG); 
        $this->assertFileSize($fileOnePNG,75000,5000  );  //make sure image size is in the right ballpark   
        unlink( $fileOnePNG ); //cleanup


        //test getting a diagram as a JPG
        $fileOneJPG = tempnam( sys_get_temp_dir() , "PHPTEST-" ) . ".jpg" ;
        $gliffy->getDiagramAsImage($firstId,Gliffy::MIME_TYPE_JPEG,$fileOneJPG); 
        echo "JPGSize:" .  filesize( $fileOneJPG );
        $this->assertTrue( filesize( $fileOneJPG ) > 110000 );  //make sure image size is in the right ballpark   
        $this->assertTrue( filesize( $fileOneJPG ) < 120000 );  //make sure image size is in the right ballpark   
        unlink( $fileOneJPG ); //cleanup 

        //test getting a diagram as a SVG 
        $fileOneSVG = tempnam( sys_get_temp_dir() , "PHPTEST-") . ".svg";
        $gliffy->getDiagramAsImage($firstId,Gliffy::MIME_TYPE_SVG,$fileOneSVG); 
        echo "SVGSize:" .  filesize( $fileOneSVG );
        $this->assertTrue( filesize( $fileOneSVG ) > 500000 );  //make sure image size is in the right ballpark   
        $this->assertTrue( filesize( $fileOneSVG ) < 550000 );  //make sure image size is in the right ballpark   
        unlink( $fileOneSVG ); //cleanup 


        //Test that the URL's we get for diagrams are correct
        $urlOnePNG = $gliffy->getDiagramAsURL($firstId,Gliffy::MIME_TYPE_PNG); 
        $this->assertURLisGood( $urlOnePNG ); 

        $urlOneJPG = $gliffy->getDiagramAsURL($firstId,Gliffy::MIME_TYPE_JPEG); 
        $this->assertURLisGood( $urlOneJPG ); 

        $urlOneSVG = $gliffy->getDiagramAsURL($firstId,Gliffy::MIME_TYPE_SVG); 
        $this->assertURLisGood( $urlOneSVG ); 

        $diagramList = $gliffy->getUserDiagrams( $gliffyAdminUsername );

        //make sure we onlt got 2 diagrams
        $this->assertTrue( sizeof( $diagramList ) == 2 ); 

        //log the user out
        $gliffy->deleteToken();
    } 


    function stripXMLHeader( $str ) {
        return trim( str_replace(  "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>", "", $str ) );   
    }

}

?>
