import React, { useState, useEffect, useRef } from "react";
import PanoramaViewer from "./components/PanoramaViewer";
import SettingsPanel from "./components/SettingsPanel";
import data from "./data/panoramas.json";
import "./styles/index.css";

function App() {
  const { categories } = data;
  const [activeCategory, setActiveCategory] = useState(categories[0]);
  const [currentPanorama, setCurrentPanorama] = useState(
    categories[0].panoramas[0]
  );
  const [openMenuId, setOpenMenuId] = useState(null);
  const [isMobile, setIsMobile] = useState(false);
  const [isSettingsOpen, setIsSettingsOpen] = useState(false);
  const navRef = useRef(null);

  // Detect mobile/touch device
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth <= 768 || "ontouchstart" in window);
    };

    checkMobile();
    window.addEventListener("resize", checkMobile);
    return () => window.removeEventListener("resize", checkMobile);
  }, []);

  // Close menu when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (navRef.current && !navRef.current.contains(event.target)) {
        setOpenMenuId(null);
      }
    };

    document.addEventListener("click", handleClickOutside);
    return () => document.removeEventListener("click", handleClickOutside);
  }, []);

  const handlePanoramaChange = (panoramaId) => {
    for (const cat of categories) {
      const pano = cat.panoramas.find((p) => p.id === panoramaId);
      if (pano) {
        setActiveCategory(cat);
        setCurrentPanorama(pano);
        setOpenMenuId(null);
        return;
      }
    }
  };

  const handlePanoramaSelect = (category, panorama) => {
    setActiveCategory(category);
    setCurrentPanorama(panorama);
    setOpenMenuId(null);
  };

  const handleTabClick = (e, catId) => {
    if (isMobile) {
      e.stopPropagation();
      setOpenMenuId(openMenuId === catId ? null : catId);
    }
  };

  const toggleSettings = () => {
    setIsSettingsOpen(!isSettingsOpen);
    setOpenMenuId(null);
  };

  return (
    <div className="App">
      {/* Branding & Location Info */}
      <div className="branding-container">
        <div className="logo-wrapper">
          {/* Thay thế src bằng link logo thực tế của bạn */}
          <img src="" alt="The  Logo" className="logo-image" />
        </div>
        <div className="location-label">
          <h2 className="location-name">{currentPanorama.name}</h2>
        </div>
      </div>

      {/* Top Navigation Bar */}
      <nav className="top-nav" ref={navRef}>
        <div className="nav-tabs">
          {categories.map((cat) => (
            <div
              key={cat.id}
              className={`nav-tab-wrapper ${
                activeCategory.id === cat.id ? "active" : ""
              } ${openMenuId === cat.id ? "open" : ""}`}
            >
              <span
                className="nav-tab"
                onClick={(e) => handleTabClick(e, cat.id)}
              >
                {cat.name}
              </span>
              {/* Submenu dropdown */}
              <div className="submenu">
                <div className="submenu-columns">
                  {cat.panoramas.map((pano) => (
                    <div
                      key={pano.id}
                      onClick={() => handlePanoramaSelect(cat, pano)}
                      className={`submenu-item ${
                        currentPanorama.id === pano.id ? "active" : ""
                      }`}
                    >
                      <span className="submenu-icon">●</span>
                      <span className="submenu-name">{pano.name}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ))}
        </div>
        <div className="nav-actions">
          <button className="nav-icon-btn" title="Thông tin">
            <span>i</span>
          </button>
          <button
            className={`nav-icon-btn ${isSettingsOpen ? "active" : ""}`}
            title="Cài đặt"
            onClick={toggleSettings}
          >
            <span>⚙</span>
          </button>
        </div>
      </nav>

      {/* Settings Panel */}
      <SettingsPanel
        isOpen={isSettingsOpen}
        onClose={() => setIsSettingsOpen(false)}
      />

      {/* Panorama Viewer */}
      <PanoramaViewer
        panorama={currentPanorama}
        onHotspotClick={handlePanoramaChange}
      />
    </div>
  );
}

export default App;
