/**
 * Holiday Calendar JavaScript
 * Handles calendar rendering and navigation
 */

class HolidayCalendar {
    constructor(holidays) {
        this.holidays = holidays || [];
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        
        this.monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        
        this.dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateCalendar();
    }
    
    bindEvents() {
        // Add event listeners for navigation buttons
        const prevBtn = document.querySelector('.nav-btn[onclick="changeMonth(-1)"]');
        const nextBtn = document.querySelector('.nav-btn[onclick="changeMonth(1)"]');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.changeMonth(-1));
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.changeMonth(1));
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.changeMonth(-1);
            } else if (e.key === 'ArrowRight') {
                this.changeMonth(1);
            }
        });
    }
    
    updateCalendar() {
        this.updateMonthDisplay();
        this.generateCalendar();
        this.updateHolidayList();
    }
    
    updateMonthDisplay() {
        const monthDisplay = document.getElementById('currentMonth');
        if (monthDisplay) {
            monthDisplay.textContent = `${this.monthNames[this.currentMonth]} ${this.currentYear}`;
        }
    }
    
    generateCalendar() {
        const calendarGrid = document.getElementById('calendarGrid');
        if (!calendarGrid) return;
        
        calendarGrid.innerHTML = '';
        
        // Add day headers
        this.dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        // Get first day of month and number of days
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();
        
        // Add empty cells for days before the first day of the month
        for (let i = 0; i < startingDayOfWeek; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            const prevMonth = new Date(this.currentYear, this.currentMonth, 0);
            const prevDay = prevMonth.getDate() - startingDayOfWeek + i + 1;
            emptyDay.innerHTML = `<div class="day-number">${prevDay}</div>`;
            calendarGrid.appendChild(emptyDay);
        }
        
        // Add days of the current month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = this.createDayElement(day);
            calendarGrid.appendChild(dayElement);
        }
        
        // Add empty cells for days after the last day of the month
        const totalCells = calendarGrid.children.length - 7; // Subtract day headers
        const remainingCells = 42 - totalCells; // 6 rows * 7 days = 42 total cells
        
        for (let i = 1; i <= remainingCells; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            emptyDay.innerHTML = `<div class="day-number">${i}</div>`;
            calendarGrid.appendChild(emptyDay);
        }
    }
    
    createDayElement(day) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        const currentDateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const today = new Date();
        const isToday = currentDateStr === `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        
        // Check if this day is a holiday
        const holidayForDay = this.holidays.find(h => h.date === currentDateStr);
        
        if (isToday) {
            dayElement.classList.add('today');
        } else if (holidayForDay) {
            dayElement.classList.add('holiday');
        }
        
        let dayContent = `<div class="day-number">${day}</div>`;
        
        if (holidayForDay) {
            dayContent += `<div class="holiday-indicator">${this.truncateText(holidayForDay.name, 20)}</div>`;
        }
        
        dayElement.innerHTML = dayContent;
        
        // Add click event for day details
        dayElement.addEventListener('click', () => {
            this.showDayDetails(day, holidayForDay);
        });
        
        return dayElement;
    }
    
    updateHolidayList() {
        const holidayList = document.getElementById('holidayList');
        if (!holidayList) return;
        
        const monthHolidays = this.holidays.filter(holiday => {
            const holidayDate = new Date(holiday.date);
            return holidayDate.getMonth() === this.currentMonth && 
                   holidayDate.getFullYear() === this.currentYear;
        });
        
        if (monthHolidays.length === 0) {
            holidayList.innerHTML = '<div class="no-holidays">No holidays this month</div>';
            return;
        }
        
        // Sort holidays by date
        monthHolidays.sort((a, b) => new Date(a.date) - new Date(b.date));
        
        holidayList.innerHTML = '';
        monthHolidays.forEach(holiday => {
            const holidayItem = this.createHolidayItem(holiday);
            holidayList.appendChild(holidayItem);
        });
    }
    
    createHolidayItem(holiday) {
        const holidayItem = document.createElement('div');
        holidayItem.className = 'holiday-item';
        
        const holidayDate = new Date(holiday.date);
        const formattedDate = holidayDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        holidayItem.innerHTML = `
            <div class="holiday-name">
                <i class="fas fa-star text-warning me-2"></i>
                ${holiday.name}
            </div>
            <div class="holiday-date">${formattedDate}</div>
        `;
        
        // Add click event for holiday details
        holidayItem.addEventListener('click', () => {
            this.showHolidayDetails(holiday);
        });
        
        return holidayItem;
    }
    
    changeMonth(direction) {
        this.currentMonth += direction;
        
        if (this.currentMonth < 0) {
            this.currentMonth = 11;
            this.currentYear--;
        } else if (this.currentMonth > 11) {
            this.currentMonth = 0;
            this.currentYear++;
        }
        
        this.updateCalendar();
        this.onMonthChange();
    }
    
    showDayDetails(day, holiday) {
        if (holiday) {
            this.showHolidayDetails(holiday);
        } else {
            const dateStr = `${this.monthNames[this.currentMonth]} ${day}, ${this.currentYear}`;
            this.showAlert(`Selected Date: ${dateStr}`, 'No holidays on this date.');
        }
    }
    
    showHolidayDetails(holiday) {
        const holidayDate = new Date(holiday.date);
        const formattedDate = holidayDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        this.showAlert(holiday.name, `Date: ${formattedDate}`);
    }
    
    showAlert(title, message) {
        // SweetAlert2 instead of simple alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert(`${title}\n${message}`);
        }
    }
    
    truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength - 3) + '...';
    }
    
    onMonthChange() {
        // Hook for custom functionality when month changes
        console.log(`Changed to ${this.monthNames[this.currentMonth]} ${this.currentYear}`);
    }
    
    // Public methods
    goToToday() {
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        this.updateCalendar();
    }
    
    goToMonth(month, year) {
        this.currentMonth = month;
        this.currentYear = year;
        this.updateCalendar();
    }
    
    addHoliday(holiday) {
        this.holidays.push(holiday);
        this.updateCalendar();
    }
    
    removeHoliday(holidayDate) {
        this.holidays = this.holidays.filter(h => h.date !== holidayDate);
        this.updateCalendar();
    }
    
    updateHolidays(holidays) {
        this.holidays = holidays;
        this.updateCalendar();
    }
    
    exportCalendar() {
        // Export calendar data
        return {
            currentMonth: this.currentMonth,
            currentYear: this.currentYear,
            holidays: this.holidays
        };
    }
}

// Global functions for backward compatibility
let holidayCalendar;

function initHolidayCalendar(holidays) {
    holidayCalendar = new HolidayCalendar(holidays);
}

function changeMonth(direction) {
    if (holidayCalendar) {
        holidayCalendar.changeMonth(direction);
    }
}

function goToToday() {
    if (holidayCalendar) {
        holidayCalendar.goToToday();
    }
}

function showHolidayMonthSelector() {
    if (!holidayCalendar) return;
    
    // Get all unique months with holidays
    const holidayMonths = {};
    holidayCalendar.holidays.forEach(holiday => {
        const date = new Date(holiday.date);
        const monthKey = `${date.getFullYear()}-${date.getMonth()}`;
        const monthName = `${holidayCalendar.monthNames[date.getMonth()]} ${date.getFullYear()}`;
        
        if (!holidayMonths[monthKey]) {
            holidayMonths[monthKey] = {
                month: date.getMonth(),
                year: date.getFullYear(),
                name: monthName,
                holidays: []
            };
        }
        holidayMonths[monthKey].holidays.push(holiday.name);
    });
    
    // Sort by date
    const sortedMonths = Object.values(holidayMonths).sort((a, b) => {
        return (a.year * 12 + a.month) - (b.year * 12 + b.month);
    });
    
    // Build modal content
    const modalBody = document.getElementById('holidayMonthList');
    
    if (sortedMonths.length === 0) {
        modalBody.innerHTML = `
            <div class="text-center p-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">No holidays found in the calendar.</p>
                <a href="/dashboard/holidays/create" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-1"></i>
                    Add Holiday
                </a>
            </div>
        `;
    } else {
        let html = '<div class="list-group">';
        sortedMonths.forEach(month => {
            html += `
                <button type="button" 
                        class="list-group-item list-group-item-action" 
                        onclick="jumpToMonth(${month.month}, ${month.year})">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                ${month.name}
                            </h6>
                            <small class="text-muted">
                                ${month.holidays.length} holiday${month.holidays.length > 1 ? 's' : ''}: 
                                ${month.holidays.join(', ')}
                            </small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </button>
            `;
        });
        html += '</div>';
        modalBody.innerHTML = html;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('holidayMonthModal'));
    modal.show();
}

function jumpToMonth(month, year) {
    if (holidayCalendar) {
        holidayCalendar.goToMonth(month, year);
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('holidayMonthModal'));
        if (modal) {
            modal.hide();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if holidays data is available globally
    if (typeof window.holidaysData !== 'undefined') {
        initHolidayCalendar(window.holidaysData);
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HolidayCalendar;
}