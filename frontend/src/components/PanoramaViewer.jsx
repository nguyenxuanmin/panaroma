import React, { useEffect, useRef } from "react";
import { ReactPhotoSphereViewer } from "react-photo-sphere-viewer";
import { MarkersPlugin } from "@photo-sphere-viewer/markers-plugin";
import "@photo-sphere-viewer/markers-plugin/index.css";

export default function PanoramaViewer({ panorama, onHotspotClick }) {
  const viewerRef = useRef(null);
  const defaultImage =
    "https://photo-sphere-viewer-data.netlify.app/assets/sphere.jpg";

  // Chuyển đổi hotspots từ JSON sang format của MarkersPlugin
  const markers =
    panorama?.hotspots?.map((hotspot) => ({
      id: hotspot.id,
      position: {
        yaw: `${hotspot.yaw}deg`,
        pitch: `${hotspot.pitch}deg`,
      },
      html: `
      <div class="custom-hotspot">
        <div class="hotspot-label">${hotspot.tooltip || ""}</div>
        <div class="hotspot-stem"></div>
        <div class="hotspot-dot"></div>
      </div>
    `,
      anchor: "bottom center", // Đặt điểm neo ở dưới cùng (tại vị trí dấu chấm)
      data: {
        targetPanorama: hotspot.targetPanorama,
      },
    })) || [];

  const handleReady = (instance) => {
    viewerRef.current = instance;

    // Lắng nghe sự kiện click để lấy tọa độ Yaw và Pitch
    /*     instance.addEventListener("click", (e) => {
      if (e.data) {
        // Chuyển đổi radian sang độ (degree) và làm tròn
        const yaw = (e.data.yaw * (180 / Math.PI)).toFixed(2);
        const pitch = (e.data.pitch * (180 / Math.PI)).toFixed(2);

        console.log(`Tọa độ đã chọn: "yaw": ${yaw}, "pitch": ${pitch}`);

        // Bạn có thể dùng alert để copy cho nhanh trên trình duyệt
        // alert(`Tọa độ:\nyaw: ${yaw}\npitch: ${pitch}`);
      }
    });
 */
    // Lấy plugin markers
    const markersPlugin = instance.getPlugin(MarkersPlugin);

    if (markersPlugin) {
      // Xử lý click vào marker
      markersPlugin.addEventListener("select-marker", (e) => {
        const targetId = e.marker.data?.targetPanorama;
        if (targetId && onHotspotClick) {
          onHotspotClick(targetId);
        }
      });
    }
  };

  const plugins = [
    [
      MarkersPlugin,
      {
        markers: markers,
      },
    ],
  ];

  return (
    <div
      className="panorama-wrapper"
      style={{ width: "100%", height: "100vh" }}
    >
      <ReactPhotoSphereViewer
        key={panorama?.id} // Force re-render khi đổi panorama
        src={panorama?.url || defaultImage}
        height={"100vh"}
        width={"100%"}
        container={""}
        navbar={["zoom", "move", "fullscreen"]}
        plugins={plugins}
        onReady={handleReady}
        defaultYaw={`${panorama?.defaultView?.yaw || 0}deg`}
        defaultPitch={`${panorama?.defaultView?.pitch || 0}deg`}
      />
    </div>
  );
}
