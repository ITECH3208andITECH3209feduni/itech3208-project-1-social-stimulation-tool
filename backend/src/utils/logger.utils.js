import env from "#config/env.config.js";

// Log levels
const LogLevel = {
    INFO: "INFO",
    WARN: "WARN",
    ERROR: "ERROR",
    DEBUG: "DEBUG",
};

const CURRENT_LEVEL = env.LOG_LEVEL || "DEBUG";

const shouldLog = (level) => {
    const levels = ["DEBUG", "INFO", "WARN", "ERROR"];
    return levels.indexOf(level) >= levels.indexOf(CURRENT_LEVEL);
};

// Timestamp helper
const timestamp = () => new Date().toISOString();

// Export logger object
const loggerUtil = {
    info: (message, data) => {
        if (!shouldLog(LogLevel.INFO)) return;
        console.log(`[${timestamp()}] [INFO] ${message}`, data ?? "");
    },
    warn: (message, data) => {
        if (!shouldLog(LogLevel.WARN)) return;
        console.warn(`[${timestamp()}] [WARN] ${message}`, data ?? "");
    },
    error: (message, data) => {
        if (!shouldLog(LogLevel.ERROR)) return;
        console.error(`[${timestamp()}] [ERROR] ${message}`, data ?? "");
    },
    debug: (message, data) => {
        if (!shouldLog(LogLevel.DEBUG)) return;
        console.debug(`[${timestamp()}] [DEBUG] ${message}`, data ?? "");
    },
};

export default loggerUtil;
