/**
 * BUILDINGS DATA - DEPRECATED
 * Source of truth mới là src/buildings/presets/*
 * Giữ file này để backward-compat: các import cũ `from "../buildings/buildingsData"` vẫn chạy.
 * @deprecated dùng `import { buildingPresetList } from "./presets"` hoặc `from "./buildingService"`
 */
import { buildingPresetList } from "./presets/index.js";

const buildingsData = buildingPresetList;

export default buildingsData;
