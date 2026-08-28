import React, { useRef, useState, useCallback } from "react";
import { ReactPhotoSphereViewer } from "react-photo-sphere-viewer";
import { MarkersPlugin } from "@photo-sphere-viewer/markers-plugin";
import "@photo-sphere-viewer/markers-plugin/index.css";
import MapMinimap from "../MapMinimap/MapMinimap";
import "./PanoramaViewer.css";

export default function PanoramaViewer({ panorama, floor, onHotspotClick, onSelectPanorama }) {
  const viewerRef = useRef(null);
  const [currentYaw, setCurrentYaw] = useState(panorama?.defaultView?.yaw || 0);
  const [showMinimap, setShowMinimap] = useState(true);
  const [miniScale, setMiniScale] = useState(1);
  const [transitionPhase, setTransitionPhase] = useState("idle");
  const defaultImage = "https://photo-sphere-viewer-data.netlify.app/assets/sphere.jpg";

  const withFadeTransition = useCallback((cb) => {
    setTransitionPhase("out");
    setTimeout(() => {
      cb();
      setTransitionPhase("in");
      setTimeout(() => setTransitionPhase("idle"), 600);
    }, 450);
  }, []);

  const markers =
    panorama?.hotspots?.map((hotspot) => ({
      id: hotspot.id,
      position: { yaw: `${hotspot.yaw}deg`, pitch: `${hotspot.pitch}deg` },
      html: `
        <div class="scene-hotspot-pin">
          <div class="scene-hotspot-badge">${hotspot.tooltip || hotspot.targetPanorama || "Đi tiếp"}</div>
          <div class="scene-hotspot-pointer">▼</div>
          <div class="scene-hotspot-ring-wrap">
            <div class="scene-hotspot-ring"></div>
            <div class="scene-hotspot-pulse"></div>
            <div class="scene-hotspot-pulse scene-hotspot-pulse--delay"></div>
          </div>
        </div>
      `,
      anchor: "bottom center",
      data: { targetPanorama: hotspot.targetPanorama },
    })) || [];

  const handleReady = (instance) => {
    viewerRef.current = instance;
    instance.addEventListener("position-updated", (e) => {
      if (e.position) setCurrentYaw((e.position.yaw * (180 / Math.PI)) % 360);
    });
    const markersPlugin = instance.getPlugin(MarkersPlugin);
    if (markersPlugin) {
      markersPlugin.addEventListener("select-marker", (e) => {
        const targetId = e.marker.data?.targetPanorama;
        if (targetId && onHotspotClick) withFadeTransition(() => onHotspotClick(targetId));
      });
    }
  };

  const handlePanoZoomIn = () => viewerRef.current?.zoom(viewerRef.current.getZoomLevel() + 15);
  const handlePanoZoomOut = () => viewerRef.current?.zoom(viewerRef.current.getZoomLevel() - 15);
  const handleToggleFullscreen = () => viewerRef.current?.toggleFullscreen();

  const plugins = [[MarkersPlugin, { markers }]];

  return (
    <div className="panorama-viewer-container">
      {/* Unified minimap - thay thế MiniMap cũ [Ảnh 2 thay Ảnh 3] - mặc định nhỏ, phóng to to hơn */}
      <div className="pano-unified-minimap-wrap">
        {showMinimap && (
          <MapMinimap
            floor={floor}
            activePanorama={panorama}
            onSelectPanorama={onSelectPanorama}
            scale={miniScale}
            currentYaw={currentYaw}
          />
        )}
        <div className="mm-ctrls">
          <button className="mm-ctrl-btn" onClick={() => setShowMinimap((v) => !v)} title={showMinimap ? "Ẩn minimap" : "Hiện minimap"}>
            {showMinimap ? "«" : "»"}
          </button>
          <button
            className="mm-ctrl-btn"
            onClick={() => setMiniScale((s) => (s === 1 ? 1.75 : s === 1.75 ? 2.6 : 1))}
            title="Phóng to minimap (1x → 1.75x → 2.6x)"
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

      <div className={`pano-scene-wrapper pano-scene-${transitionPhase}`}>
        <ReactPhotoSphereViewer
          key={panorama?.id}
          src={panorama?.url || defaultImage}
          height={"100vh"}
          width={"100%"}
          container={""}
          navbar={false}
          plugins={plugins}
          onReady={handleReady}
          defaultYaw={`${panorama?.defaultView?.yaw || 0}deg`}
          defaultPitch={`${panorama?.defaultView?.pitch || 0}deg`}
        />
      </div>

      <div className="pano-bottomleft-toolbar">
        <button className="bottom-tool-btn" onClick={handlePanoZoomIn} title="Phóng to 360">+</button>
        <button className="bottom-tool-btn" onClick={handlePanoZoomOut} title="Thu nhỏ 360">-</button>
        <button className="bottom-tool-btn" title="Thông tin">ℹ</button>
        <button className="bottom-tool-btn" onClick={handleToggleFullscreen} title="Toàn màn hình">⛶</button>
      </div>
    </div>
  );
}
