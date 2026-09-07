import React from "react";
import "./HelpModal.css";

export default function HelpModal({ isOpen, onClose }) {
  if (!isOpen) return null;

  return (
    <div className="help-modal-backdrop" onClick={onClose}>
      <div className="help-modal-container" onClick={(e) => e.stopPropagation()}>
        <div className="help-modal-header">
          <h3>Hướng dẫn sử dụng</h3>
          <button className="help-close-btn" onClick={onClose}>×</button>
        </div>
        <div className="help-modal-body">
          <div className="help-item">
            <span className="help-icon">🏠</span>
            <div>
              <strong>Trang chủ / 外構:</strong> Quay về góc nhìn tổng quan toàn cảnh dự án.
            </div>
          </div>
          <div className="help-item">
            <span className="help-icon">📹</span>
            <div>
              <strong>Xem Video (ムービー):</strong> Mở video giới thiệu chi tiết từng khu vực.
            </div>
          </div>
          <div className="help-item">
            <span className="help-icon">🖼</span>
            <div>
              <strong>Đổi chế độ xem:</strong> Chuyển đổi qua lại giữa Mặt bằng 2D và Panaroma 360°.
            </div>
          </div>
          <div className="help-item">
            <span className="help-icon">🗺</span>
            <div>
              <strong>Bản đồ thu nhỏ (MiniMap):</strong> Hiện trên các tầng để dễ dàng định vị điểm xem.
            </div>
          </div>
          <div className="help-item">
            <span className="help-icon">∨</span>
            <div>
              <strong>Ẩn/Hiện thanh ảnh:</strong> Bấm nút mũi tên xuống ở góc dưới để ẩn/hiện danh sách ảnh thumbnails.
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
