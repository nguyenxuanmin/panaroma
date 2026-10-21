import React, { useState } from "react";
import "./FooterCarousel.css";

const VISIBLE_COUNT = 6;

export default function FooterCarousel({ panaromas, activePanaroma, onSelectPanaroma, floorId }) {
  const [collapsed, setCollapsed] = useState(false);
  const [start, setStart] = useState(0);
  const [stateKey, setStateKey] = useState({ floorId, activeId: activePanaroma?.id ?? null });

  const list = React.useMemo(() => panaromas || [], [panaromas]);

  // Đổi tầng → về đầu danh sách; đổi pano active (chọn từ map/sidebar) → dồn cửa sổ cho thấy nó
  const activeId = activePanaroma?.id ?? null;
  if (stateKey.floorId !== floorId || stateKey.activeId !== activeId) {
    setStateKey({ floorId, activeId });
    if (stateKey.floorId !== floorId) setStart(0);
    else if (activeId) {
      const idx = list.findIndex((p) => p.id === activeId);
      if (idx >= 0) setStart(Math.min(idx, Math.max(0, list.length - VISIBLE_COUNT)));
    }
  }

  const maxStart = Math.max(0, list.length - VISIBLE_COUNT);
  const safeStart = Math.min(Math.max(start, 0), maxStart);
  const visiblePanaromas = list.slice(safeStart, safeStart + VISIBLE_COUNT);
  const canScroll = list.length > VISIBLE_COUNT;
  const canPrev = canScroll && safeStart > 0;
  const canNext = canScroll && safeStart < maxStart;

  // Trượt từng card một, đến đầu/cuối thì dừng (không wrap-around)
  const handleScrollLeft = () => setStart((v) => Math.max(0, v - 1));
  const handleScrollRight = () => setStart((v) => Math.min(maxStart, v + 1));

  return (
    <div className={`footer-wrap ${collapsed ? "collapsed" : ""}`}>
      {!collapsed && (
        <footer className="footer-carousel-container">
          {canScroll && (
            <button className="carousel-nav-circle-btn" onClick={handleScrollLeft} aria-label="Previous" disabled={!canPrev}>
              <svg viewBox="0 0 24 24" width="16" height="16" stroke="#4a4a4a" strokeWidth="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
          )}
          <div className="carousel-track">
            {visiblePanaromas.map((pano) => {
              const isActive = activePanaroma?.id === pano.id;
              return (
                <div key={pano.id} className={`carousel-card ${isActive ? "active" : ""}`} onClick={() => onSelectPanaroma(pano)} title={`${pano.name}`}>
                  <div className="card-thumb-wrapper">
                    <img src={pano.thumbnail || pano.url} alt={pano.name} className="card-thumb-img" loading="lazy" />
                  </div>
                  <div className="card-title-bar"><span className="card-title-text">{pano.name}</span></div>
                </div>
              );
            })}
          </div>
          {canScroll && (
            <button className="carousel-nav-circle-btn" onClick={handleScrollRight} aria-label="Next" disabled={!canNext}>
              <svg viewBox="0 0 24 24" width="16" height="16" stroke="#4a4a4a" strokeWidth="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
          )}
        </footer>
      )}
      <button className="footer-collapse-bar" onClick={() => setCollapsed((v) => !v)} title={collapsed ? "Open panaroma list" : "Close panaroma list"}>
        <span className={`collapse-icon ${collapsed ? "up" : "down"}`}>{collapsed ? "︿" : "﹀"}</span>
      </button>
    </div>
  );
}
