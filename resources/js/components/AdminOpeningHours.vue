<template>
  <div>
    <!-- Main schedule management section -->
    <div class="schedule-container">
      <h2 class="text-xl font-medium text-gray-800 mb-4">Regular Schedule</h2>
      
      <div v-if="loading" class="loading-overlay">
        <div class="spinner"></div>
      </div>
      
      <div v-else-if="error" class="error-message">
        {{ error }}
      </div>
      
      <div v-else>
        <div v-for="(schedule, index) in weeklySchedule" :key="index" class="day-row">
          <div class="day-name">{{ schedule.day }}</div>
          
          <div class="status-toggle">
            <label class="toggle-label">
              <input type="checkbox" v-model="schedule.isOpen">
              <span>{{ schedule.isOpen ? 'Open' : 'Closed' }}</span>
            </label>
          </div>
          
          <div class="time-inputs" v-if="schedule.isOpen">
            <div class="time-select-group">
              <select 
                v-model="schedule.openHour" 
                class="time-select" 
                :disabled="!schedule.isOpen"
                @change="updateTimeValue(schedule, 'open')"
              >
                <option v-for="hour in hours" :key="hour" :value="hour">{{ formatHour(hour) }}</option>
              </select>
              <span class="time-colon">:</span>
              <select 
                v-model="schedule.openMinute" 
                class="time-select" 
                :disabled="!schedule.isOpen"
                @change="updateTimeValue(schedule, 'open')"
              >
                <option v-for="minute in minutes" :key="minute" :value="minute">{{ minute }}</option>
              </select>
            </div>
            <span class="time-separator">to</span>
            <div class="time-select-group">
              <select 
                v-model="schedule.closeHour" 
                class="time-select" 
                :disabled="!schedule.isOpen"
                @change="updateTimeValue(schedule, 'close')"
              >
                <option v-for="hour in hours" :key="hour" :value="hour">{{ formatHour(hour) }}</option>
              </select>
              <span class="time-colon">:</span>
              <select 
                v-model="schedule.closeMinute" 
                class="time-select" 
                :disabled="!schedule.isOpen"
                @change="updateTimeValue(schedule, 'close')"
              >
                <option v-for="minute in minutes" :key="minute" :value="minute">{{ minute }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Special Saturday schedule section -->
    <div class="saturday-schedule">
      <h2 class="saturday-heading">Biweekly Saturday Schedule</h2>
      
      <div class="status-toggle">
        <label class="toggle-label">
          <input type="checkbox" v-model="saturdaySchedule.enabled">
          <span>Enable biweekly Saturday opening</span>
        </label>
      </div>
      
      <div v-if="saturdaySchedule.enabled" class="time-inputs mt-4">
        <div>
          <label class="block text-sm font-medium mb-1">Open Time</label>
          <div class="time-select-group">
            <select 
              v-model="saturdaySchedule.openHour" 
              class="time-select"
              @change="updateSaturdayTimeValue('open')"
            >
              <option v-for="hour in hours" :key="hour" :value="hour">{{ formatHour(hour) }}</option>
            </select>
            <span class="time-colon">:</span>
            <select 
              v-model="saturdaySchedule.openMinute" 
              class="time-select"
              @change="updateSaturdayTimeValue('open')"
            >
              <option v-for="minute in minutes" :key="minute" :value="minute">{{ minute }}</option>
            </select>
          </div>
        </div>
        <span class="time-separator">to</span>
        <div>
          <label class="block text-sm font-medium mb-1">Close Time</label>
          <div class="time-select-group">
            <select 
              v-model="saturdaySchedule.closeHour" 
              class="time-select"
              @change="updateSaturdayTimeValue('close')"
            >
              <option v-for="hour in hours" :key="hour" :value="hour">{{ formatHour(hour) }}</option>
            </select>
            <span class="time-colon">:</span>
            <select 
              v-model="saturdaySchedule.closeMinute" 
              class="time-select"
              @change="updateSaturdayTimeValue('close')"
            >
              <option v-for="minute in minutes" :key="minute" :value="minute">{{ minute }}</option>
            </select>
          </div>
        </div>
      </div>
      
      <div v-if="saturdaySchedule.enabled" class="next-date">
        <span>Next Saturday Opening:</span>
        <span class="next-date-value">{{ nextSaturdayDate }}</span>
      </div>
      
      <button @click="saveChanges" class="save-button">
        Save Changes
      </button>
    </div>
    
    <!-- Day Edit Modal -->
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">
            Edit {{ editingDay ? editingDay.day : '' }} Hours
          </h3>
          <button @click="closeModal" class="modal-close">&times;</button>
        </div>
        
        <div class="modal-body">
          <div class="status-toggle">
            <label class="toggle-label">
              <input type="checkbox" v-model="editingDay.isOpen">
              <span>{{ editingDay?.isOpen ? 'Open on this day' : 'Closed on this day' }}</span>
            </label>
          </div>
          
          <div v-if="editingDay?.isOpen" class="time-inputs mt-4">
            <div>
              <label class="block text-sm font-medium mb-1">Open Time</label>
              <input
                type="time"
                v-model="editingDay.openTime"
                class="time-input"
              />
            </div>
            <span class="time-separator">to</span>
            <div>
              <label class="block text-sm font-medium mb-1">Close Time</label>
              <input
                type="time"
                v-model="editingDay.closeTime"
                class="time-input"
              />
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button @click="closeModal" class="modal-button cancel-button">
            Cancel
          </button>
          <button @click="saveDay" class="modal-button save-modal-button">
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';

export default {
  data() {
    return {
      weeklySchedule: [
        { day: 'Monday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
        { day: 'Tuesday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
        { day: 'Wednesday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
        { day: 'Thursday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
        { day: 'Friday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
        { day: 'Saturday', isOpen: false, openTime: '09:00', closeTime: '13:00', openHour: '09', openMinute: '00', closeHour: '13', closeMinute: '00' },
        { day: 'Sunday', isOpen: false, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
      ],
      saturdaySchedule: {
        enabled: false,
        openTime: '09:00',
        closeTime: '13:00',
        openHour: '09',
        openMinute: '00',
        closeHour: '13',
        closeMinute: '00',
        nextDate: null
      },
      editingDay: null,
      editingIndex: -1,
      showModal: false,
      loading: true,
      error: null,
      isSaving: false,
      originalSchedule: null,
      hours: Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0')),
      minutes: ['00', '15', '30', '45']
    };
  },
  computed: {
    nextSaturdayDate() {
      if (!this.saturdaySchedule.nextDate) {
        return 'Not scheduled';
      }
      return moment(this.saturdaySchedule.nextDate).format('MMMM D, YYYY');
    },
    hasUnsavedChanges() {
      if (!this.originalSchedule) return false;
      
      // Compare weekly schedule
      const weeklyChanged = JSON.stringify(this.weeklySchedule) !== 
                           JSON.stringify(this.originalSchedule.weeklySchedule);
      
      // Compare saturday schedule
      const satChanged = this.saturdaySchedule.enabled !== this.originalSchedule.saturdaySchedule.enabled ||
                        this.saturdaySchedule.openTime !== this.originalSchedule.saturdaySchedule.openTime ||
                        this.saturdaySchedule.closeTime !== this.originalSchedule.saturdaySchedule.closeTime;
      
      return weeklyChanged || satChanged;
    }
  },
  mounted() {
    this.fetchSchedule();
    
    // Warn user if they try to leave with unsaved changes
    window.addEventListener('beforeunload', this.warnIfUnsavedChanges);
  },
  beforeDestroy() {
    window.removeEventListener('beforeunload', this.warnIfUnsavedChanges);
  },
  methods: {
    formatHour(hour) {
      const hourNum = parseInt(hour, 10);
      const period = hourNum >= 12 ? 'PM' : 'AM';
      const hour12 = hourNum % 12 || 12;
      return `${hour12} ${period}`;
    },
    
    updateTimeValue(schedule, type) {
      if (type === 'open') {
        schedule.openTime = `${schedule.openHour}:${schedule.openMinute}`;
      } else {
        schedule.closeTime = `${schedule.closeHour}:${schedule.closeMinute}`;
      }
    },
    
    updateSaturdayTimeValue(type) {
      if (type === 'open') {
        this.saturdaySchedule.openTime = `${this.saturdaySchedule.openHour}:${this.saturdaySchedule.openMinute}`;
      } else {
        this.saturdaySchedule.closeTime = `${this.saturdaySchedule.closeHour}:${this.saturdaySchedule.closeMinute}`;
      }
    },
    
    fetchSchedule() {
      this.loading = true;
      this.error = null;
      
      axios.get('/api/admin/opening-hours')
        .then(response => {
          // Populate weekly schedule
          if (response.data.weeklySchedule) {
            this.weeklySchedule = response.data.weeklySchedule.map(day => {
              const formattedDay = {
                ...day,
                openTime: this.formatTime(day.openTime),
                closeTime: this.formatTime(day.closeTime)
              };
              
              // Split time into hours and minutes for the dropdowns
              const [openHour, openMinute] = formattedDay.openTime.split(':');
              const [closeHour, closeMinute] = formattedDay.closeTime.split(':');
              
              return {
                ...formattedDay,
                openHour,
                openMinute,
                closeHour,
                closeMinute
              };
            });
          }
          
          // Populate Saturday schedule
          if (response.data.saturdaySchedule) {
            const satSchedule = {
              enabled: response.data.saturdaySchedule.enabled || false,
              openTime: this.formatTime(response.data.saturdaySchedule.openTime),
              closeTime: this.formatTime(response.data.saturdaySchedule.closeTime),
              nextDate: response.data.saturdaySchedule.nextDate
            };
            
            // Split time into hours and minutes for the dropdowns
            const [openHour, openMinute] = satSchedule.openTime.split(':');
            const [closeHour, closeMinute] = satSchedule.closeTime.split(':');
            
            this.saturdaySchedule = {
              ...satSchedule,
              openHour,
              openMinute,
              closeHour,
              closeMinute
            };
          }
          
          // Store original data for change detection
          this.originalSchedule = {
            weeklySchedule: JSON.parse(JSON.stringify(this.weeklySchedule)),
            saturdaySchedule: { ...this.saturdaySchedule }
          };
          
          this.loading = false;
        })
        .catch(error => {
          this.error = 'Failed to load schedule. Please try again.';
          this.loading = false;
          console.error('Schedule fetch error:', error);
        });
    },
    
    saveChanges() {
      if (this.isSaving) return;
      
      // Validate before saving
      const invalidDay = this.validateFullSchedule();
      if (invalidDay) {
        alert(`Invalid times for ${invalidDay}. Close time must be after open time.`);
        return;
      }
      
      this.isSaving = true;
      
      // Prepare data for API
      const scheduleData = {
        weeklySchedule: this.weeklySchedule.map(day => ({
          day: day.day,
          isOpen: day.isOpen,
          openTime: day.openTime,
          closeTime: day.closeTime
        })),
        saturdaySchedule: {
          enabled: this.saturdaySchedule.enabled,
          openTime: this.saturdaySchedule.openTime,
          closeTime: this.saturdaySchedule.closeTime
        }
      };
      
      axios.post('/api/admin/opening-hours/bulk', scheduleData)
        .then(response => {
          // Handle successful update
          if (response.data.saturdaySchedule) {
            this.saturdaySchedule.nextDate = response.data.saturdaySchedule.nextDate;
          }
          
          // Update original data reference to match current state
          this.originalSchedule = {
            weeklySchedule: JSON.parse(JSON.stringify(this.weeklySchedule)),
            saturdaySchedule: { ...this.saturdaySchedule }
          };
          
          // Show success message
          alert('Schedule updated successfully');
          this.isSaving = false;
        })
        .catch(error => {
          // Handle error
          alert('Failed to update schedule. Please try again.');
          console.error('Save error:', error);
          this.isSaving = false;
        });
    },
    
    validateFullSchedule() {
      // Check all days for valid times
      for (const day of this.weeklySchedule) {
        if (day.isOpen && day.openTime >= day.closeTime) {
          return day.day;
        }
      }
      
      // Check Saturday special schedule
      if (this.saturdaySchedule.enabled && 
          this.saturdaySchedule.openTime >= this.saturdaySchedule.closeTime) {
        return 'Biweekly Saturday';
      }
      
      return null;
    },
    
    editDay(index) {
      this.editingIndex = index;
      this.editingDay = JSON.parse(JSON.stringify(this.weeklySchedule[index]));
      this.showModal = true;
    },
    
    saveDay() {
      // Validate times
      if (this.editingDay.isOpen) {
        if (!this.editingDay.openTime || !this.editingDay.closeTime) {
          alert('Please specify both open and close times');
          return;
        }
        
        // Check if close time is after open time
        if (this.editingDay.openTime >= this.editingDay.closeTime) {
          alert('Close time must be after open time');
          return;
        }
      }
      
      // Update the schedule
      this.weeklySchedule[this.editingIndex] = JSON.parse(JSON.stringify(this.editingDay));
      this.closeModal();
    },
    
    closeModal() {
      this.showModal = false;
      this.editingDay = null;
      this.editingIndex = -1;
    },
    
    formatTime(time) {
      if (!time) return '00:00';
      
      // Check if time is already in HH:MM format
      if (typeof time === 'string' && time.match(/^\d{2}:\d{2}$/)) {
        return time;
      }
      
      // Otherwise format using moment
      return moment(time, 'HH:mm').format('HH:mm');
    },
    
    warnIfUnsavedChanges(event) {
      if (this.hasUnsavedChanges) {
        event.preventDefault();
        event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return event.returnValue;
      }
    },
    
    resetToDefaults() {
      if (confirm('Are you sure you want to reset to default hours? This will discard all changes.')) {
        this.weeklySchedule = [
          { day: 'Monday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
          { day: 'Tuesday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
          { day: 'Wednesday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
          { day: 'Thursday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
          { day: 'Friday', isOpen: true, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
          { day: 'Saturday', isOpen: false, openTime: '09:00', closeTime: '13:00', openHour: '09', openMinute: '00', closeHour: '13', closeMinute: '00' },
          { day: 'Sunday', isOpen: false, openTime: '09:00', closeTime: '17:00', openHour: '09', openMinute: '00', closeHour: '17', closeMinute: '00' },
        ];
        
        this.saturdaySchedule = {
          enabled: false,
          openTime: '09:00',
          closeTime: '13:00',
          openHour: '09',
          openMinute: '00',
          closeHour: '13',
          closeMinute: '00',
          nextDate: null
        };
      }
    }
  }
};
</script>

<style scoped>
.schedule-container {
  margin-bottom: 2rem;
  background-color: #fff;
  border-radius: 0.5rem;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.day-row {
  display: grid;
  grid-template-columns: 120px 150px 1fr;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #edf2f7;
}

.day-name {
  font-weight: 600;
  color: #2d3748;
  font-size: 16px;
}

.status-toggle {
  display: flex;
  align-items: center;
}

.toggle-label {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 16px;
}

.toggle-label input {
  margin-right: 0.75rem;
  width: 1.25rem;
  height: 1.25rem;
}

.time-inputs {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

.time-select-group {
  display: flex;
  align-items: center;
}

.time-select {
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  padding: 0.75rem;
  font-size: 16px;
  width: auto;
  min-width: 90px;
  transition: all 0.2s;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

.time-colon {
  margin: 0 0.5rem;
  font-weight: bold;
  font-size: 18px;
}

.time-select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}

.time-separator {
  color: #718096;
  font-weight: 500;
  font-size: 16px;
  padding: 0 0.5rem;
}

.saturday-schedule {
  background-color: #f7fafc;
  border-radius: 0.5rem;
  padding: 2rem;
  margin-bottom: 2rem;
  border: 1px solid #e2e8f0;
}

.saturday-heading {
  font-size: 1.25rem;
  font-weight: 500;
  color: #2d3748;
  margin-bottom: 1.5rem;
}

.next-date {
  margin-top: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 16px;
}

.next-date-value {
  font-weight: 600;
  color: #4299e1;
  font-size: 16px;
}

.save-button {
  margin-top: 2rem;
  background-color: #4299e1;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  font-weight: 500;
  font-size: 16px;
  transition: background-color 0.2s;
}

.save-button:hover {
  background-color: #3182ce;
}

.loading-overlay {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 3rem;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 4px solid rgba(66, 153, 225, 0.3);
  border-radius: 50%;
  border-top-color: #4299e1;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.error-message {
  color: #e53e3e;
  padding: 1.5rem;
  background-color: #fff5f5;
  border-radius: 0.375rem;
  margin: 1rem 0;
  font-size: 16px;
}

/* Make schedule more mobile-friendly */
@media (max-width: 768px) {
  .day-row {
    grid-template-columns: 1fr;
    grid-gap: 0.75rem;
    padding: 1.25rem 0;
  }
  
  .time-inputs {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .time-separator {
    margin: 0.5rem 0;
  }
}

/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #2d3748;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #718096;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  padding: 1rem 1.5rem;
  border-top: 1px solid #e2e8f0;
  gap: 0.75rem;
}

.modal-button {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
}

.cancel-button {
  background-color: #edf2f7;
  color: #2d3748;
  transition: background-color 0.2s;
}

.cancel-button:hover {
  background-color: #e2e8f0;
}

.save-modal-button {
  background-color: #4299e1;
  color: white;
  transition: background-color 0.2s;
}

.save-modal-button:hover {
  background-color: #3182ce;
}
</style> 