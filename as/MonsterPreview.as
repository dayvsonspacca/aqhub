package
{
   import flash.display.Loader;
   import flash.display.MovieClip;
   import flash.events.Event;
   import flash.events.IOErrorEvent;
   import flash.events.SecurityErrorEvent;
   import flash.geom.Rectangle;
   import flash.net.URLRequest;
   import flash.net.navigateToURL;
   import flash.system.ApplicationDomain;
   import flash.system.LoaderContext;

   public class MonsterPreview extends MovieClip
   {
      private static const SERVER_PATH:String = "http://127.0.0.1:8000/proxy/swf/monster/";
      private static const FIT_RATIO:Number    = 0.8;

      internal var pLoaderD:ApplicationDomain = new ApplicationDomain(ApplicationDomain.currentDomain);
      internal var pLoaderC:LoaderContext      = new LoaderContext(false, pLoaderD);

      internal var mcStage:MovieClip;
      internal var monsterMC:MovieClip;
      internal var sLink:String = "";
      internal var sAnim:String = "";

      public function MonsterPreview()
      {
         super();
         trace("[MonsterPreview] Constructor started");
         mcStage = MovieClip(addChild(new MovieClip()));
         trace("[MonsterPreview] Constructor complete");
      }

      public function getLabels() : String
      {
         if (monsterMC == null) return "";
         var labelNames:Array = [];
         var labels:Array = monsterMC.currentLabels;
         for (var i:int = 0; i < labels.length; i++)
         {
            labelNames.push(labels[i].name);
         }
         return labelNames.join(",");
      }

      public function loadMonster(sFile:String, sSymbol:String, initialAnim:String = "") : void
      {
         sLink = sSymbol;
         sAnim = initialAnim;

         var url:String = SERVER_PATH + sFile;
         trace("[MonsterPreview] Loading -> " + url);

         var loader:Loader = new Loader();
         var info:*        = loader.contentLoaderInfo;

         info.addEventListener(Event.OPEN,                        onOpen,          false, 0, true);
         info.addEventListener(Event.COMPLETE,                    onComplete,      false, 0, true);
         info.addEventListener(IOErrorEvent.IO_ERROR,             onIOError,       false, 0, true);
         info.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError, false, 0, true);

         loader.load(new URLRequest(url), pLoaderC);
      }

      public function playAnim(anim:String) : void
      {
         if (monsterMC == null)
         {
            trace("[MonsterPreview] playAnim: monsterMC not loaded yet");
            return;
         }
         if (hasLabel(anim))
         {
            trace("[MonsterPreview] Playing anim: " + anim);
            monsterMC.gotoAndPlay(anim);
         }
         else
         {
            trace("[MonsterPreview] Anim not found: " + anim);
         }
      }

      private function hasLabel(anim:String) : Boolean
      {
         var labels:Array = monsterMC.currentLabels;
         for (var i:int = 0; i < labels.length; i++)
         {
            if (labels[i].name == anim) return true;
         }
         return false;
      }

      private function notifyLabelsToJS(labelsCSV:String) : void
      {
         var js:String = "javascript:window.onMonsterLabelsLoaded && window.onMonsterLabelsLoaded('" + labelsCSV + "')";
         try
         {
            navigateToURL(new URLRequest(js), "_self");
            trace("[MonsterPreview] Labels sent to JS: " + labelsCSV);
         }
         catch (err:Error)
         {
            trace("[MonsterPreview] navigateToURL failed: " + err.message);
         }
      }

      private function onOpen(e:Event) : void
      {
         trace("[MonsterPreview] Connection opened -> " + e.target.url);
      }

      private function onComplete(e:Event) : void
      {
         trace("[MonsterPreview] SWF loaded, trying getDefinition('" + sLink + "')");

         var mc:MovieClip = null;

         try
         {
            var AssetClass:Class = pLoaderD.getDefinition(sLink) as Class;
            mc = new AssetClass();
            trace("[MonsterPreview] getDefinition OK");
         }
         catch (err:Error)
         {
            trace("[MonsterPreview] getDefinition failed: " + err.message);
            trace("[MonsterPreview] Trying direct content as fallback");
            mc = e.target.content as MovieClip;
         }

         if (mc == null)
         {
            trace("[MonsterPreview] ERROR: mc is null, nothing to display");
            return;
         }

         if (mc.scaleX == 0)
         {
            trace("[MonsterPreview] scaleX was 0, fixing to 1");
            mc.scaleX = 1;
         }

         monsterMC = mc;
         logLabels(mc);

         mcStage.addChild(mc);
         fitAndCenter(mc);

         stage.addEventListener(Event.RESIZE, onStageResize, false, 0, true);

         notifyLabelsToJS(getLabels());

         if (sAnim != "")
         {
            trace("[MonsterPreview] Auto-playing initial anim: " + sAnim);
            playAnim(sAnim);
         }

         trace("[MonsterPreview] Monster added to stage");
      }

      private function fitAndCenter(mc:MovieClip) : void
      {
         var sw:Number = stage.stageWidth;
         var sh:Number = stage.stageHeight;

         var bounds:Rectangle = mc.getBounds(mc);

         trace("[MonsterPreview] Bounds -> x=" + bounds.x + " y=" + bounds.y + " w=" + bounds.width + " h=" + bounds.height);

         var fitSize:Number   = Math.min(sw, sh) * FIT_RATIO;
         var scale:Number     = fitSize / Math.max(bounds.width, bounds.height);
         mc.scaleX = mc.scaleY = scale;

         trace("[MonsterPreview] Scale applied: " + scale);

         mc.x = 0;
         mc.y = 0;

         var boundsAfter:Rectangle = mc.getBounds(this);
         mc.x += (sw / 2) - (boundsAfter.x + boundsAfter.width  / 2);
         mc.y += (sh / 2) - (boundsAfter.y + boundsAfter.height / 2);

         trace("[MonsterPreview] Position -> x=" + mc.x + " y=" + mc.y);
      }

      private function onStageResize(e:Event) : void
      {
         if (monsterMC != null)
         {
            fitAndCenter(monsterMC);
         }
      }

      private function logLabels(mc:MovieClip) : void
      {
         var labels:Array = mc.currentLabels;
         trace("[MonsterPreview] Found " + labels.length + " animation labels:");
         for (var i:int = 0; i < labels.length; i++)
         {
            trace("[MonsterPreview]   [" + i + "] " + labels[i].name + " (frame " + labels[i].frame + ")");
         }
      }

      private function onIOError(e:IOErrorEvent) : void
      {
         trace("[MonsterPreview] IO ERROR: " + e.text);
      }

      private function onSecurityError(e:SecurityErrorEvent) : void
      {
         trace("[MonsterPreview] SECURITY ERROR (CORS): " + e.text);
      }
   }
}
