import React, { useEffect, useState, useCallback } from "react";
import BuildingSidebar from "./components/BuildingSidebar/BuildingSidebar";
import FloorMap from "./components/FloorMap/FloorMap";
import FooterCarousel from "./components/FooterCarousel/FooterCarousel";
import PanoramaViewer from "./components/PanoramaViewer/PanoramaViewer";
import TopHeader from "./components/TopHeader/TopHeader";
import SettingsPanel from "./components/SettingsPanel";
import GoogleMapModal from "./components/GoogleMapModal/GoogleMapModal";
import VideoModal from "./components/VideoModal/VideoModal";
import RotatePrompt from "./components/RotatePrompt/RotatePrompt";
import "./styles/index.css";

const backendUrl = (import.meta.env.VITE_BACKEND_URL || "");

function resolveAssetUrl(path) {
  if (!path) return "";
  if (/^https?:\/\//i.test(path)) return path;
  return `${backendUrl}/${path.replace(/^\//, "")}`;
}

function createProjectFromFloors(floors) {
  return {
    id: "laravel-project",
    name: "Laravel Panorama",
    buildings: [{
      id: "laravel-building",
      name: "Panorama Building",
      type: "group",
      floors: floors.map((floor) => ({
        ...floor,
        planImage: resolveAssetUrl(floor.planImage),
        panoramas: (floor.panoramas || []).map((panorama) => ({
          ...panorama,
          thumbnail: resolveAssetUrl(panorama.thumbnail),
          url: resolveAssetUrl(panorama.url || panorama.thumbnail),
        })),
      })),
    }],
  };
}

function App() {
  const [projects, setProjects] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [apiError, setApiError] = useState("");
  const [selectedProjectId, setSelectedProjectId] = useState("");
  const selectedProject = projects.find((p) => p.id === selectedProjectId) || projects[0];
  const buildings = selectedProject?.buildings || [];

  const [activeBuilding, setActiveBuilding] = useState(null);
  const [activeFloorId, setActiveFloorId] = useState(null);
  const activeFloor = activeBuilding?.type === "group"
    ? activeBuilding.floors.find((floor) => floor.id === activeFloorId) || activeBuilding.floors[0]
    : activeBuilding;
  const [activePanorama, setActivePanorama] = useState(null);
  const [viewMode, setViewMode] = useState("map");
  const [isSettingsOpen, setIsSettingsOpen] = useState(false);
  const [showGmap, setShowGmap] = useState(false);
  const [showVideo, setShowVideo] = useState(false);
  const [showSidebar, setShowSidebar] = useState(true);

  useEffect(() => {
    fetch("/api/floors")
      .then((response) => {
        if (!response.ok) throw new Error("Không thể tải dữ liệu panorama.");
        return response.json();
      })
      .then((floors) => {
        const project = createProjectFromFloors(floors);
        const building = project.buildings[0];
        const floor = building.floors[0];
        setProjects([project]);
        setSelectedProjectId(project.id);
        setActiveBuilding(building);
        setActiveFloorId(floor?.id || null);
        setActivePanorama(floor?.panoramas?.find((pano) => pano.id === floor.defaultPanoramaId) || floor?.panoramas?.[0] || null);
      })
      .catch((error) => setApiError(error.message))
      .finally(() => setIsLoading(false));
  }, []);

  const handleSelectBuilding = (building) => {
    setActiveBuilding(building);
    if (building.type === "single") {
      setActiveFloorId(null);
      if (building.panoramas?.length) setActivePanorama(building.panoramas[0]);
    } else {
      const first = building.floors[0];
      setActiveFloorId(first.id);
      if (first.panoramas?.length) setActivePanorama(first.panoramas[0]);
    }
  };

  const handleSelectFloor = (floor) => {
    setActiveFloorId(floor.id);
    if (floor.panoramas?.length) setActivePanorama(floor.panoramas[0]);
  };

  const findPanoramaById = (pid) => {
    for (const b of buildings) {
      if (b.type === "single") {
        const pano = b.panoramas.find((p) => p.id === pid);
        if (pano) return { building: b, floor: b, panorama: pano };
      } else {
        for (const f of b.floors) {
          const pano = f.panoramas.find((p) => p.id === pid);
          if (pano) return { building: b, floor: f, panorama: pano };
        }
      }
    }
    return null;
  };

  const handleSelectProject = (pid) => {
    const proj = projects.find((p) => p.id === pid);
    if (!proj) return;
    setSelectedProjectId(pid);
    const bs = proj.buildings;
    const nb = bs[0];
    setActiveBuilding(nb);
    const fid = nb.type === "group" ? nb.floors[0].id : null;
    setActiveFloorId(fid);
    const nf = fid ? nb.floors[0] : nb;
    setActivePanorama(nf.panoramas[0]);
    setViewMode("map");
  };

  const handleHotspot3DClick = (targetPanoramaId) => {
    const found = findPanoramaById(targetPanoramaId);
    if (found) {
      setActiveBuilding(found.building);
      if (found.building.type === "group") setActiveFloorId(found.floor.id);
      else setActiveFloorId(null);
      setActivePanorama(found.panorama);
    }
  };

  const handleMapPanoramaClick = (pano) => {
    const found = findPanoramaById(pano.id);
    if (found) {
      setActiveBuilding(found.building);
      if (found.building.type === "group") setActiveFloorId(found.floor.id);
      else setActiveFloorId(null);
    }
    setActivePanorama(pano);
    setViewMode("panorama");
  };

  // Footer chỉ show thumbnail của tầng hiện tại, tối đa 6

  const handleToggleFullscreen = useCallback(() => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen?.();
    } else {
      document.exitFullscreen?.();
    }
  }, []);

  if (isLoading) return <div className="app-loading">Đang tải dữ liệu panorama...</div>;
  if (apiError) return <div className="app-loading app-loading-error">{apiError}</div>;
  if (!activeBuilding || !activeFloor || !activePanorama) {
    return <div className="app-loading app-loading-error">Chưa có dữ liệu panorama.</div>;
  }

  return (
    <div className="app-layout">
      <TopHeader
        activeBuilding={activeBuilding}
        activeFloor={activeFloor}
        activePanorama={activePanorama}
        viewMode={viewMode}
        onToggleViewMode={(mode) => setViewMode(mode)}
        onOpenGoogleMap={() => setShowGmap(true)}
        onOpenVideo={() => setShowVideo(true)}
        onToggleFullscreen={handleToggleFullscreen}
        onToggleSidebar={() => setShowSidebar((v) => !v)}
        projects={projects}
        selectedProjectId={selectedProjectId}
        onSelectProject={handleSelectProject}
      />

      <main className="main-viewport">
        {viewMode === "map" ? (
          <FloorMap floor={activeFloor} building={activeBuilding} activePanorama={activePanorama} onSelectPanorama={handleMapPanoramaClick} />
        ) : (
          <PanoramaViewer
            panorama={activePanorama}
            floor={activeFloor}
            building={activeBuilding}
            onHotspotClick={handleHotspot3DClick}
            onSelectPanorama={(pano) => setActivePanorama(pano)}
            onReturnToMap={() => setViewMode("map")}
          />
        )}
        {showSidebar && (
          <BuildingSidebar
            buildings={buildings}
            activeBuilding={activeBuilding}
            activeFloor={activeFloor}
            onSelectBuilding={handleSelectBuilding}
            onSelectFloor={handleSelectFloor}
          />
        )}
        <FooterCarousel panoramas={activeFloor.panoramas} activePanorama={activePanorama} onSelectPanorama={handleMapPanoramaClick} floorId={activeFloor.id} />
      </main>

      <SettingsPanel isOpen={isSettingsOpen} onClose={() => setIsSettingsOpen(false)} />
      <GoogleMapModal isOpen={showGmap} onClose={() => setShowGmap(false)} />
      <VideoModal isOpen={showVideo} onClose={() => setShowVideo(false)} videos={activeFloor?.videos || []} floorName={activeFloor?.name} />
      <RotatePrompt />
    </div>
  );
}

export default App;
