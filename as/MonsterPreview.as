package
{
   import flash.display.Loader;
   import flash.display.MovieClip;
   import flash.events.Event;
   import flash.events.IOErrorEvent;
   import flash.events.SecurityErrorEvent;
   import flash.geom.Rectangle;
   import flash.external.ExternalInterface;
   import flash.net.URLRequest;
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

      public function getAnimations() : String
      {
         if (monsterMC == null) return "";
         var animationNames:Array = [];
         var frames:Array = monsterMC.currentLabels;
         for (var i:int = 0; i < frames.length; i++)
         {
            animationNames.push(frames[i].name);
         }
         return animationNames.join(",");
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
         if (hasAnimation(anim))
         {
            trace("[MonsterPreview] Playing anim: " + anim);
            monsterMC.gotoAndPlay(anim);
         }
         else
         {
            trace("[MonsterPreview] Anim not found: " + anim);
         }
      }

      private function hasAnimation(anim:String) : Boolean
      {
         var frames:Array = monsterMC.currentLabels;
         for (var i:int = 0; i < frames.length; i++)
         {
            if (frames[i].name == anim) return true;
         }
         return false;
      }

      private function notifyAnimations(animationsCSV:String) : void
      {
         if (!ExternalInterface.available) { return; }

         ExternalInterface.call("onMonsterAnimationsLoaded", animationsCSV);
         trace("[MonsterPreview] Animations sent: " + animationsCSV);
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
         logAnimations(mc);

         mcStage.addChild(mc);
         fitAndCenter(mc);

         stage.addEventListener(Event.RESIZE, onStageResize, false, 0, true);

         notifyAnimations(getAnimations());

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

      private function logAnimations(mc:MovieClip) : void
      {
         var frames:Array = mc.currentLabels;
         trace("[MonsterPreview] Found " + frames.length + " animations:");
         for (var i:int = 0; i < frames.length; i++)
         {
            trace("[MonsterPreview]   [" + i + "] " + frames[i].name + " (frame " + frames[i].frame + ")");
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
