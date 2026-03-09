package
{
   import flash.display.MovieClip;
   import flash.display.Shape;
   import flash.display.StageAlign;
   import flash.display.StageScaleMode;
   import flash.events.Event;
   import flash.external.ExternalInterface;

   public class Main extends MovieClip
   {
      private static const BG_COLOR:uint = 0x1f202a;

      private var bg:Shape;
      private var preview:MonsterPreview;

      public function Main()
      {
         super();

         stage.align     = StageAlign.TOP_LEFT;
         stage.scaleMode = StageScaleMode.NO_SCALE;

         bg = new Shape();
         drawBackground();
         addChild(bg);

         stage.addEventListener(Event.RESIZE, onStageResize, false, 0, true);

         var params:Object  = stage.loaderInfo.parameters;
         var sFile:String   = params.sFile   || "monster-VoidKnight.swf";
         var sSymbol:String = params.sSymbol || "VoidKnight";
         var sAnim:String   = params.sAnim   || "Idle";

         trace("[Main] sFile="   + sFile);
         trace("[Main] sSymbol=" + sSymbol);
         trace("[Main] sAnim="   + sAnim);

         preview = new MonsterPreview();
         addChild(preview);

         registerExternalInterface();

         preview.loadMonster(sFile, sSymbol, sAnim);
      }

      private function drawBackground() : void
      {
         bg.graphics.clear();
         bg.graphics.beginFill(BG_COLOR);
         bg.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
         bg.graphics.endFill();
      }

      private function onStageResize(e:Event) : void
      {
         drawBackground();
      }

      private function registerExternalInterface() : void
      {
         if (!ExternalInterface.available) { return; }

         ExternalInterface.addCallback("playAnim", onPlayAnimCallback);
         trace("[Main] ExternalInterface: playAnim registered");
      }

      private function onPlayAnimCallback(anim:String) : void
      {
         preview.playAnim(anim);
      }
   }
}
