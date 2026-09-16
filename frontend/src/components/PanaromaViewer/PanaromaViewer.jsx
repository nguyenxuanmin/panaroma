import React, { useRef, useState, useCallback, useEffect, useMemo } from "react";
import { ReactPhotoSphereViewer } from "react-photo-sphere-viewer";
import { MarkersPlugin } from "@photo-sphere-viewer/markers-plugin";
import "@photo-sphere-viewer/markers-plugin/index.css";
import MapMinimap from "../MapMinimap/MapMinimap";
import "./PanaromaViewer.css";

export default function PanaromaViewer({ panaroma, floor, onHotspotClick, onSelectPanaroma, showLeftToolbar = true, onReturnToMap }) {
  const viewerRef = useRef(null);
  const getInitialAngle = (p) => {
    if (typeof p?.mapPosition?.angle === "number") return p.mapPosition.angle;
    return p?.defaultView?.yaw ?? 0;
  };
  const [currentYaw, setCurrentYaw] = useState(getInitialAngle(panaroma));
  const [showMinimap, setShowMinimap] = useState(true);
  const [miniScale, setMiniScale] = useState(1);
  const [transitionPhase, setTransitionPhase] = useState("idle");
  const [visiblePanaroma, setVisiblePanaroma] = useState(panaroma);
  const defaultImage = "https://photo-sphere-viewer-data.netlify.app/assets/sphere.jpg";

  // ---- Đa option ảnh cho 1 vị trí ----
  const imageOptions = useMemo(() => {
    if (!panaroma) return [];
    const extras = panaroma.images || panaroma.panaromaImages || [];
    const main = {
      id: `${panaroma.id}__main`,
      title: panaroma.name || "Ảnh chính",
      thumbnail: panaroma.thumbnail || panaroma.url,
      url: panaroma.url || panaroma.thumbnail,
      isMain: true,
    };
    const extraOpts = (Array.isArray(extras) ? extras : []).map((img, idx) => ({
      id: String(img.id ?? `${panaroma.id}__opt_${idx}`),
      title: img.title || `Option ${idx + 1}`,
      thumbnail: img.thumbnail || img.url,
      url: img.url || img.thumbnail,
      isMain: false,
    }));
    return [main, ...extraOpts];
  }, [panaroma]);

  const hasMultipleOptions = imageOptions.length > 1;
  const [activeOptionIdx, setActiveOptionIdx] = useState(0);

  // Reset option khi đổi vị trí panaroma
  useEffect(() => {
    setActiveOptionIdx(0);
  }, [panaroma?.id]);

  const activeOption = imageOptions[activeOptionIdx] || imageOptions[0] || null;
  const currentUrl = activeOption?.url || panaroma?.url || defaultImage;
  const currentThumb = activeOption?.thumbnail || panaroma?.thumbnail || panaroma?.url;

  // Preload khi đổi panaroma (theo id) — giữ logic cũ nhưng dùng currentUrl khi cần
  useEffect(() => {
    if (!panaroma || panaroma.id === visiblePanaroma?.id) return;
    let cancelled = false;
    // luôn preload ảnh main của panaroma mới; option sẽ reset về 0
    const url = panaroma.url || defaultImage;
    const img = new window.Image();
    img.src = url;
    const done = () => {
      if (cancelled) return;
      setVisiblePanaroma(panaroma);
      setCurrentYaw(getInitialAngle(panaroma));
      setTransitionPhase("idle");
    };
    if (img.complete) done();
    else {
      img.onload = done;
      img.onerror = done;
    }
    return () => { cancelled = true; };
  }, [panaroma, visiblePanaroma?.id]);

  // Preload khi đổi option trong cùng vị trí
  const [preloadedUrl, setPreloadedUrl] = useState(currentUrl);
  useEffect(() => {
    if (currentUrl === preloadedUrl) return;
    const img = new window.Image();
    img.src = currentUrl;
    const done = () => setPreloadedUrl(currentUrl);
    if (img.complete) done();
    else {
      img.onload = done;
      img.onerror = done;
    }
  }, [currentUrl, preloadedUrl]);

  const withFadeTransition = useCallback((cb) => {
    setTransitionPhase("out");
    setTimeout(() => cb(), 150);
  }, []);

  const displayPanaroma = visiblePanaroma || panaroma;
  const markers =
    displayPanaroma?.hotspots?.map((hotspot) => ({
      id: hotspot.id,
      position: { yaw: `${hotspot.yaw}deg`, pitch: `${hotspot.pitch}deg` },
      html: `
        <div class="scene-hotspot-pin">
          <div class="scene-hotspot-badge">${hotspot.tooltip || hotspot.targetPanaroma || "Go to next"}</div>
          <div class="scene-hotspot-pointer">▼</div>
          <div class="scene-hotspot-ring-wrap">
            <div class="scene-hotspot-ring"></div>
            <div class="scene-hotspot-pulse"></div>
            <div class="scene-hotspot-pulse scene-hotspot-pulse--delay"></div>
          </div>
        </div>
      `,
      anchor: "bottom center",
      data: { targetPanaroma: hotspot.targetPanaroma },
    })) || [];

  const handleReady = (instance) => {
    viewerRef.current = instance;
    instance.addEventListener("position-updated", (e) => {
      if (e.position) setCurrentYaw((e.position.yaw * (180 / Math.PI)) % 360);
    });
    const markersPlugin = instance.getPlugin(MarkersPlugin);
    if (markersPlugin) {
      markersPlugin.addEventListener("select-marker", (e) => {
        const targetId = e.marker.data?.targetPanaroma;
        if (!targetId || !onHotspotClick) return;
        const markerId = e.marker.id;
        const hotspot = displayPanaroma?.hotspots?.find((h) => h.id === markerId || h.targetPanaroma === targetId);
        if (hotspot && viewerRef.current) {
          try {
            viewerRef.current.animate({
              yaw: `${hotspot.yaw}deg`,
              pitch: `${hotspot.pitch}deg`,
              zoom: 75,
              speed: 2000,
            });
          } catch {}
          setTimeout(() => onHotspotClick(targetId), 2000);
        } else {
          withFadeTransition(() => onHotspotClick(targetId));
        }
      });
    }
  };

  const handlePanoZoomIn = () => viewerRef.current?.zoom(viewerRef.current.getZoomLevel() + 15);
  const handlePanoZoomOut = () => viewerRef.current?.zoom(viewerRef.current.getZoomLevel() - 15);
  const handleToggleFullscreen = () => viewerRef.current?.toggleFullscreen();

  const plugins = [[MarkersPlugin, { markers }]];

  return (
    <div className="panaroma-viewer-container">
      <div
        className="pano-backdrop"
        style={{ backgroundImage: `url(${currentThumb || displayPanaroma?.thumbnail || displayPanaroma?.url || ""})` }}
      />
      {showLeftToolbar && (
        <div className="pano-unified-minimap-wrap">
          {showMinimap && (
            <MapMinimap
              floor={floor}
              activePanaroma={panaroma}
              onSelectPanaroma={onSelectPanaroma}
              scale={miniScale}
              currentYaw={currentYaw}
            />
          )}
          <div className="mm-ctrls">
            <button className="mm-ctrl-btn" onClick={() => setShowMinimap((v) => !v)} title={showMinimap ? "Hide minimap" : "Show minimap"}>
              {showMinimap ? "«" : "»"}
            </button>
            <button
              className="mm-ctrl-btn"
              onClick={() => setMiniScale((s) => (s === 1 ? 1.75 : s === 1.75 ? 2.6 : 1))}
              title="Zoom in on minimap"
            >
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="1.8">
                <circle cx="11" cy="11" r="7" />
                <line x1="11" y1="8" x2="11" y2="14" />
                <line x1="8" y1="11" x2="14" y2="11" />
                <line x1="21" y1="21" x2="16.5" y2="16.5" />
              </svg>
            </button>
          </div>
        </div>
      )}

      {/* Đa option ảnh cho 1 vị trí - hiển thị khi có >1 ảnh */}
      {hasMultipleOptions && (
        <div className="pano-image-options">
          <div className="pano-image-options-track">
            {imageOptions.map((opt, idx) => {
              const isActive = idx === activeOptionIdx;
              return (
                <button
                  key={opt.id}
                  className={`pano-image-opt ${isActive ? "active" : ""}`}
                  onClick={() => setActiveOptionIdx(idx)}
                  title={opt.title}
                >
                  <img src={opt.thumbnail || opt.url} alt={opt.title} className="pano-image-opt-thumb" loading="lazy" />
                </button>
              );
            })}
          </div>
        </div>
      )}

      <div className={`pano-scene-wrapper pano-scene-${transitionPhase}`}>
        <ReactPhotoSphereViewer
          key={`${displayPanaroma?.id}__opt_${activeOptionIdx}__${preloadedUrl}`}
          src={preloadedUrl || currentUrl || defaultImage}
          height={"100%"}
          width={"100%"}
          container={""}
          navbar={false}
          plugins={plugins}
          onReady={handleReady}
          defaultYaw={`${getInitialAngle(displayPanaroma)}deg`}
          defaultPitch={`${displayPanaroma?.defaultView?.pitch || 0}deg`}
        />
      </div>

      {showLeftToolbar && (
        <div className="pano-bottomleft-toolbar">
          <button className="bottom-tool-btn" onClick={handlePanoZoomIn} title="Zoom in 360">+</button>
          <button className="bottom-tool-btn" onClick={handlePanoZoomOut} title="Zoom out 360">-</button>
          <button className="bottom-tool-btn" onClick={handleToggleFullscreen} title="Full screen">⛶</button>
        </div>
      )}
    </div>
  );
}
