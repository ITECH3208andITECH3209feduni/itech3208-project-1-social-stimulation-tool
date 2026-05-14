const isDev = import.meta.env.MODE === "development";

const LogLevel = {
    INFO: { label: "INFO", color: "#3182ce" }, // Blue
    WARN: { label: "WARN", color: "#dd6b20" }, // Orange
    ERROR: { label: "ERROR", color: "#e53e3e" }, // Red
    DEBUG: { label: "DEBUG", color: "#718096" }, // Gray
};

const timestamp = () => new Date().toLocaleTimeString();

const print = (level, message, data) => {
    if (!isDev && level !== LogLevel.ERROR) return;

    const styles = `background: ${level.color}; color: white; padding: 2px 4px; border-radius: 3px; font-weight: bold;`;
    console.log(
        `%c${level.label}%c [${timestamp()}] ${message}`,
        styles,
        "color: inherit;",
        data ?? ""
    );
};

const loggerUtil = {
    info: (message, data) => print(LogLevel.INFO, message, data),
    warn: (message, data) => print(LogLevel.WARN, message, data),
    error: (message, data) => print(LogLevel.ERROR, message, data),
    debug: (message, data) => print(LogLevel.DEBUG, message, data),
};

export default loggerUtil;
