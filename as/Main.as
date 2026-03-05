package
{
   import flash.display.MovieClip;
   import flash.display.Shape;
   import flash.display.StageAlign;
   import flash.display.StageScaleMode;
   import flash.external.ExternalInterface;

   public class Main extends MovieClip
   {
      public function Main()
      {
         super();

         stage.align     = StageAlign.TOP_LEFT;
         stage.scaleMode = StageScaleMode.NO_SCALE;

         var bg:Shape = new Shape();
         bg.graphics.beginFill(0x1f202a);
         bg.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
         bg.graphics.endFill();
         addChild(bg);

         var params:Object = stage.loaderInfo.parameters;

         var sFile:String   = params.sFile   || "monster-VoidKnight.swf";
         var sSymbol:String = params.sSymbol || "VoidKnight";
         var sAnim:String   = params.sAnim   || "Idle";

         trace("[Main] sFile="   + sFile);
         trace("[Main] sSymbol=" + sSymbol);
         trace("[Main] sAnim="   + sAnim);

         var preview:MonsterPreview = new MonsterPreview();
         addChild(preview);

         if (ExternalInterface.available)
         {
            ExternalInterface.addCallback("playAnim", function(anim:String):void {
               preview.playAnim(anim);
            });
            trace("[Main] ExternalInterface: playAnim registered");
         }

         preview.loadMonster(sFile, sSymbol, sAnim);
      }
   }
}
