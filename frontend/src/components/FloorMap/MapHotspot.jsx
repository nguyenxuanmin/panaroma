import React from "react";

export default function MapHotspot({ panaroma, isActive, onClick, onHover }) {
  const { mapPosition, name, label } = panaroma;
  const displayLabel = label || name;

  return (
    <div
      className={`map-hotspot ${isActive ? "active" : ""}`}
      style={{ left: `${mapPosition.x}%`, top: `${mapPosition.y}%` }}
      onClick={(e) => { e.stopPropagation(); onClick(panaroma); }}
      onMouseEnter={() => onHover && onHover(panaroma)}
      onMouseLeave={() => onHover && onHover(null)}
      title={`${displayLabel} (Click to view 360°)`}
    >
      {/* Black label bar + leader line wrapper */}
      <div className="hotspot-label-wrap">
        <div className="hotspot-label">{displayLabel}</div>
        <div className="hotspot-leader"></div>
      </div>

      {/* Pulse + Red dot */}
      <div className="hotspot-pulse-ring"></div>
      <div className="hotspot-dot"></div>
    </div>
  );
}
