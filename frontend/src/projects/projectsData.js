import { getBuildingsByIds } from "../buildings/buildingService.js";

/**
 * Helper: tạo buildings array từ danh sách buildingIds.
 * Sau này admin chỉ cần lưu buildingIds vào DB thay vì embed toàn bộ object.
 */
function resolveBuildings(ids) {
  return getBuildingsByIds(ids);
}

// Preset: danh sách id mặc định (dùng lại cho mọi project)
// Muốn project nào dùng building khác chỉ cần đổi mảng này.
const DEFAULT_BUILDING_IDS = ["exterior", "medical-deck", "cafe", "new1"];

export const projects = [
  {
    id: "globe3-building-1",
    name: "globe3-building 1",
    // Lưu tham chiếu thay vì embed - dễ tách DB sau này
    buildingIds: [...DEFAULT_BUILDING_IDS],
    buildings: resolveBuildings(DEFAULT_BUILDING_IDS),
  },
  {
    id: "project-2",
    name: "Project 2 - Demo",
    buildingIds: [...DEFAULT_BUILDING_IDS],
    buildings: resolveBuildings(DEFAULT_BUILDING_IDS),
  },
  {
    id: "project-3",
    name: "Project 3 - Sample",
    buildingIds: [...DEFAULT_BUILDING_IDS],
    buildings: resolveBuildings(DEFAULT_BUILDING_IDS),
  },
];

// Helper cho admin sau này: tạo project mới chỉ cần truyền buildingIds
export function createProject({ id, name, buildingIds }) {
  const ids = buildingIds || [...DEFAULT_BUILDING_IDS];
  return { id, name, buildingIds: ids, buildings: resolveBuildings(ids) };
}

export default projects;
